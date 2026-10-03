import { useForm } from 'react-hook-form'
import { login as loginUser } from '../api/user.js'
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';

export default function Login() {
    const navigate = useNavigate();
      const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const [loginError, setLoginError] = useState(null);

    const onSubmit = async (data) => {
        try {
            await loginUser(data.email, data.password);
            navigate('/notes');
        } catch (error) {
            setLoginError('wrong email or password');
        }
    };

    return (
        <div className="login-container">
            <div className="login-form">
                <h2>Login</h2>
                <form onSubmit={handleSubmit(onSubmit)}>
                    <div className="form-group">
                        <label htmlFor="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            {...register('email', { required: 'Email is required' })}
                        />
                        {errors.email && <span className="error">{errors.email.message}</span>}
                    </div>
                    <div className="form-group">
                        <label htmlFor="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            {...register('password', { required: 'Password is required' })}
                        />
                        {errors.password && <span className="error">{errors.password.message}</span>}
                    </div>
                    <button type="submit">Login</button>
                    <div className="error-message">
                        {loginError && <span>{loginError}</span>}
                    </div>
                </form>
            </div>
        </div>
    );
}