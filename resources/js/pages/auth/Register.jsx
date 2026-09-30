import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
	const { data, setData, post, processing, errors } = useForm({
		name: '',
		email: '',
		password: '',
		password_confirmation: '',
	});

	const submit = (event) => {
		event.preventDefault();

		post('/register');
	};

	return (
		<>
			<Head title="Register" />

			<main className="flex min-h-screen items-center justify-center bg-[#f4f7f6] p-5 font-sans">
				<section className="w-full max-w-112.5 rounded-xl bg-white p-8 shadow-[0_4px_20px_rgba(0,0,0,0.05)] sm:p-10">
					<h1 className="mb-6 mt-0 text-center text-2xl font-bold text-[#333]">
						Create account
					</h1>

					<form onSubmit={submit} className="flex flex-col gap-4">
						<div>
							<label htmlFor="name" className="sr-only">Name</label>
							<input
								id="name"
								name="name"
								type="text"
								placeholder="Name"
								autoComplete="name"
								required
								value={data.name}
								onChange={(event) => setData('name', event.target.value)}
								aria-invalid={Boolean(errors.name)}
								aria-describedby={errors.name ? 'name-error' : undefined}
								className="box-border w-full rounded-lg border border-[#ddd] p-3 text-sm text-[#333] outline-none transition-colors duration-200 placeholder:text-[#777] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
							/>
							{errors.name && <p id="name-error" role="alert" className="mt-1 text-sm text-red-600">{errors.name}</p>}
						</div>

						<div>
							<label htmlFor="email" className="sr-only">Email</label>
							<input
								id="email"
								name="email"
								type="email"
								placeholder="Email"
								autoComplete="email"
								required
								value={data.email}
								onChange={(event) => setData('email', event.target.value)}
								aria-invalid={Boolean(errors.email)}
								aria-describedby={errors.email ? 'email-error' : undefined}
								className="box-border w-full rounded-lg border border-[#ddd] p-3 text-sm text-[#333] outline-none transition-colors duration-200 placeholder:text-[#777] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
							/>
							{errors.email && <p id="email-error" role="alert" className="mt-1 text-sm text-red-600">{errors.email}</p>}
						</div>

						<div>
							<label htmlFor="password" className="sr-only">Password</label>
							<input
								id="password"
								name="password"
								type="password"
								placeholder="Password"
								autoComplete="new-password"
								required
								value={data.password}
								onChange={(event) => setData('password', event.target.value)}
								aria-invalid={Boolean(errors.password)}
								aria-describedby={errors.password ? 'password-error' : undefined}
								className="box-border w-full rounded-lg border border-[#ddd] p-3 text-sm text-[#333] outline-none transition-colors duration-200 placeholder:text-[#777] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
							/>
							{errors.password && <p id="password-error" role="alert" className="mt-1 text-sm text-red-600">{errors.password}</p>}
						</div>

						<div>
							<label htmlFor="password_confirmation" className="sr-only">Confirm password</label>
							<input
								id="password_confirmation"
								name="password_confirmation"
								type="password"
								placeholder="Confirm password"
								autoComplete="new-password"
								required
								value={data.password_confirmation}
								onChange={(event) => setData('password_confirmation', event.target.value)}
								className="box-border w-full rounded-lg border border-[#ddd] p-3 text-sm text-[#333] outline-none transition-colors duration-200 placeholder:text-[#777] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
							/>
						</div>

						<button
							type="submit"
							disabled={processing}
							className="mt-2 w-full rounded-lg border-0 bg-[#237b65] p-3 text-base font-medium text-white transition-colors duration-200 hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] disabled:cursor-not-allowed disabled:opacity-60"
						>
							{processing ? 'Creating account...' : 'Register'}
						</button>
					</form>

					<p className="mb-0 mt-5 text-center text-sm text-[#666]">
						Already have an account?{' '}
						<Link href="/login" className="text-[#237b65] underline-offset-4 hover:underline">
							Log in
						</Link>
					</p>
				</section>
			</main>
		</>
	);
}
