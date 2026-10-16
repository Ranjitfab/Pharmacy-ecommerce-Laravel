import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { validateField } from '../../validation';

const emailField = { label: 'Email', type: 'email', required: true };

export default function ForgotPassword() {
	const { data, setData, post, processing, errors, setError, clearErrors } = useForm({ email: '' });
	const { status } = usePage().props;

	const updateEmail = (value) => {
		setData('email', value);
		const error = validateField(emailField, value);

		if (error) {
			setError('email', error);
		} else {
			clearErrors('email');
		}
	};

	const submit = (event) => {
		event.preventDefault();
		post('/forgot-password');
	};

	return (
		<>
			<Head title="Forgot password" />
			<main className="flex min-h-screen items-center justify-center bg-[#f4f7f6] p-5 font-sans">
				<section className="w-full max-w-112.5 rounded-xl bg-white p-8 shadow-[0_4px_20px_rgba(0,0,0,0.05)] sm:p-10">
					<h1 className="mb-2 mt-0 text-center text-2xl font-bold text-[#26332f]">Forgot password</h1>
					<p className="mb-6 mt-0 text-center text-sm leading-6 text-[#718078]">
						Enter your account email and we’ll send a password reset link.
					</p>

					{status && (
						<p role="status" className="mb-5 rounded-md border border-[#b8d9ca] bg-[#e9f5ee] px-4 py-3 text-sm text-[#245c43]">
							{status}
						</p>
					)}

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
								onChange={(event) => updateEmail(event.target.value)}
								aria-invalid={Boolean(errors.email)}
								aria-describedby={errors.email ? 'email-error' : undefined}
								className="box-border w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
							/>
							{errors.email && <p id="email-error" role="alert" className="mb-0 mt-1.5 text-sm text-red-700">{errors.email}</p>}
						</div>
						<button
							type="submit"
							disabled={processing}
							className="mt-1 w-full rounded-md bg-[#237b65] px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] disabled:cursor-wait disabled:opacity-60"
						>
							{processing ? 'Sending link...' : 'Send reset link'}
						</button>
					</form>

					<p className="mb-0 mt-5 text-center text-sm text-[#66736d]">
						Remember your password?{' '}
						<Link href="/login" className="font-semibold text-[#237b65] underline-offset-4 hover:underline">Log in</Link>
					</p>
				</section>
			</main>
		</>
	);
}
