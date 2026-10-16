import { useForm } from 'react-hook-form'
import { useState } from 'react'
import { register as registerUser } from '../api/user'
import { useNavigate } from 'react-router-dom';


export default function Register() {
    const { handleSubmit, register, formState: { errors } } = useForm();
    const [errorMessage, setErrorMessage] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const navigate = useNavigate();


    const checkPasswordsMatch = (value) => {
        const password = document.getElementById('password').value;
        return value === password || false;
    }

    const onSubmit = async (data) => {
        setErrorMessage('');
        setSuccessMessage('');

        try {
            const passwordsMatch = checkPasswordsMatch(data.repeatPassword);
            if (!passwordsMatch) {
                setErrorMessage('Passwords do not match');
                return;
            }

            const response = await registerUser(data.email, data.password);
            setSuccessMessage(response.message);
        } catch (error) {
            setErrorMessage(error.message);
            return;
        }
    };

    return (
        <div className="register-container">
            {successMessage ? (
                <div className="success-message">
                    <p>{successMessage}</p>
                    <p>check your email for confirmation</p>
                    <button onClick={() => navigate('/login')}>Go to Login</button>
                </div>
            ) : (
            <form onSubmit={handleSubmit(onSubmit)}>
                <label htmlFor="email">email</label>
                <input type="email" id="email" name="email" {...register('email', { required: 'Email is required' })} />
                <label htmlFor="password">password</label>
                <input type="password" id="password" name="password" {...register('password', { required: 'Password is required' })} />
                <label htmlFor="repeatPassword">repeat password</label>
                <input type="password" id="repeatPassword" name="repeatPassword" {...register('repeatPassword', { required: 'Please repeat your password' })} />
                <div className="policy-consent">
                    <label htmlFor="acceptPolicy">
                        <input
                            type="checkbox"
                            id="acceptPolicy"
                            {...register('acceptPolicy', { required: 'You must accept the privacy policy and terms of service' })}
                        />
                        I agree to the <a href="/privacy-policy" target="_blank" rel="noopener noreferrer">Privacy Policy</a> and <a href="/terms-of-service" target="_blank" rel="noopener noreferrer">Terms of Service</a>.
                    </label>
                    {errors.acceptPolicy && <span className="error">{errors.acceptPolicy.message}</span>}
                </div>
                <div className="messages">
                    {errors.email && <span className="error">{errors.email.message}</span>}
                    {errors.password && <span className="error">{errors.password.message}</span>}
                    {errors.repeatPassword && <span className="error">{errors.repeatPassword.message}</span>}
                    {errors.repeatPassword && errors.repeatPassword.type === 'validate' && <span className="error">{checkPasswordsMatch(document.getElementById('repeatPassword').value)}</span>}
                    {errorMessage && <span className="error">{errorMessage}</span>}
                    {successMessage && <span className="success">{successMessage}</span>}
                </div>
                <button type="submit">Submit</button>
            </form>
            )}
        </div>
    );
}