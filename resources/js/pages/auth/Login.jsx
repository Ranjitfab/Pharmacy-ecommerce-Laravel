import { Head, Link, useForm } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (event) => {
        event.preventDefault();

        post('/login');
    };

    return (
        <>
            <Head title="Login" />

            <main className="flex min-h-screen items-center justify-center bg-[#f4f7f6] p-5 font-sans">
                <section className="w-full max-w-112.5 rounded-xl bg-white p-8 shadow-[0_4px_20px_rgba(0,0,0,0.05)] sm:p-10">
                    <h1 className="mb-6 mt-0 text-center text-2xl font-bold text-[#333]">
                        Login
                    </h1>

                    <form onSubmit={submit} className="flex flex-col gap-4">
                        <div>
                            <label htmlFor="email" className="sr-only">
                                Email
                            </label>
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
                            {errors.email && (
                                <p id="email-error" role="alert" className="mt-1 text-sm text-red-600">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="password" className="sr-only">
                                Password
                            </label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Password"
                                autoComplete="current-password"
                                required
                                value={data.password}
                                onChange={(event) => setData('password', event.target.value)}
                                aria-invalid={Boolean(errors.password)}
                                aria-describedby={errors.password ? 'password-error' : undefined}
                                className="box-border w-full rounded-lg border border-[#ddd] p-3 text-sm text-[#333] outline-none transition-colors duration-200 placeholder:text-[#777] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
                            />
                            {errors.password && (
                                <p id="password-error" role="alert" className="mt-1 text-sm text-red-600">
                                    {errors.password}
                                </p>
                            )}
                        </div>

                        <label htmlFor="remember" className="flex min-h-6 cursor-pointer items-center gap-2 text-sm text-[#555]">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                checked={data.remember}
                                onChange={(event) => setData('remember', event.target.checked)}
                                className="size-4 accent-[#237b65]"
                            />
                            Remember me
                        </label>

                        <button
                            type="submit"
                            disabled={processing}
                            className="mt-2 w-full rounded-lg border-0 bg-[#237b65] p-3 text-base font-medium text-white transition-colors duration-200 hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {processing ? 'Logging in...' : 'Login'}
                        </button>
                    </form>

                    <p className="mb-0 mt-5 text-center text-sm text-[#666]">
                        No account yet?{' '}
                        <Link href="/register" className="text-[#237b65] underline-offset-4 hover:underline">
                            Register here
                        </Link>
                    </p>
                </section>
            </main>
        </>
    );
}