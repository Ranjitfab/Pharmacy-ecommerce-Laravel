import { Head, Link, useForm } from '@inertiajs/react';
import { validateField } from '../../validation';

const resetFields = {
	email: { label: 'Email', type: 'email', required: true },
	password: { label: 'New password', required: true, minLength: 8 },
	password_confirmation: { label: 'Confirm password', required: true, matches: 'password' },
};

export default function ResetPassword({ email, token }) {
	const { data, setData, post, processing, errors, setError, clearErrors } = useForm({
		email,
		token,
		password: '',
		password_confirmation: '',
	});

	const updateField = (fieldName, value) => {
		const nextData = { ...data, [fieldName]: value };
		setData(fieldName, value);
		const error = validateField(resetFields[fieldName], value, nextData);

		if (error) {
			setError(fieldName, error);
		} else {
			clearErrors(fieldName);
		}

		if (fieldName === 'password' && nextData.password_confirmation) {
			const confirmationError = validateField(
				resetFields.password_confirmation,
				nextData.password_confirmation,
				nextData,
			);

			if (confirmationError) {
				setError('password_confirmation', confirmationError);
			} else {
				clearErrors('password_confirmation');
			}
		}
	};

	const submit = (event) => {
		event.preventDefault();
		post('/reset-password');
	};

	return (
		<>
			<Head title="Reset password" />
			<main className="flex min-h-screen items-center justify-center bg-[#f4f7f6] p-5 font-sans">
				<section className="w-full max-w-112.5 rounded-xl bg-white p-8 shadow-[0_4px_20px_rgba(0,0,0,0.05)] sm:p-10">
					<h1 className="mb-2 mt-0 text-center text-2xl font-bold text-[#26332f]">Reset password</h1>
					<p className="mb-6 mt-0 text-center text-sm text-[#718078]">Choose a new password for your account.</p>

					<form onSubmit={submit} className="flex flex-col gap-4">
						<div>
							<label htmlFor="email" className="mb-1.5 block text-sm font-medium text-[#35443e]">Email</label>
							<input
								id="email"
								name="email"
								type="email"
								autoComplete="email"
								required
								value={data.email}
								onChange={(event) => updateField('email', event.target.value)}
								aria-invalid={Boolean(errors.email)}
								aria-describedby={errors.email ? 'email-error' : undefined}
								className="box-border w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
							/>
							{errors.email && <p id="email-error" role="alert" className="mb-0 mt-1.5 text-sm text-red-700">{errors.email}</p>}
						</div>

						{['password', 'password_confirmation'].map((fieldName) => {
							const isConfirmation = fieldName === 'password_confirmation';
							const label = resetFields[fieldName].label;
							const error = errors[fieldName];
							const fieldId = `reset-${fieldName}`;

							return (
								<div key={fieldName}>
									<label htmlFor={fieldId} className="mb-1.5 block text-sm font-medium text-[#35443e]">{label}</label>
									<input
										id={fieldId}
										name={fieldName}
										type="password"
										autoComplete={isConfirmation ? 'new-password' : 'new-password'}
										required
										value={data[fieldName]}
										onChange={(event) => updateField(fieldName, event.target.value)}
										aria-invalid={Boolean(error)}
										aria-describedby={error ? `${fieldId}-error` : undefined}
										className="box-border w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
									/>
									{error && <p id={`${fieldId}-error`} role="alert" className="mb-0 mt-1.5 text-sm text-red-700">{error}</p>}
								</div>
							);
						})}

						<button
							type="submit"
							disabled={processing}
							className="mt-1 w-full rounded-md bg-[#237b65] px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] disabled:cursor-wait disabled:opacity-60"
						>
							{processing ? 'Resetting password...' : 'Reset password'}
						</button>
					</form>

					<p className="mb-0 mt-5 text-center text-sm text-[#66736d]">
						<Link href="/login" className="font-semibold text-[#237b65] underline-offset-4 hover:underline">Back to log in</Link>
					</p>
				</section>
			</main>
		</>
	);
}
