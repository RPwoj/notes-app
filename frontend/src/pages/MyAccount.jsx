import { useForm } from 'react-hook-form';
import { deleteUser, getUserData, removeStoredToken, updateUser } from '../api/user.js';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

export default function MyAccount() {
    const navigate = useNavigate();
    const [userData, setUserData] = useState(null);
    const [loadError, setLoadError] = useState('');
    const [emailMessage, setEmailMessage] = useState('');
    const [emailError, setEmailError] = useState('');
    const [passwordMessage, setPasswordMessage] = useState('');
    const [passwordError, setPasswordError] = useState('');
    const [deleteError, setDeleteError] = useState('');
    const [isDeleting, setIsDeleting] = useState(false);
    const {
        register: registerEmail,
        handleSubmit: handleEmailSubmit,
        formState: { errors: emailErrors, isSubmitting: isEmailSubmitting },
    } = useForm();
    const {
        register: registerPassword,
        handleSubmit: handlePasswordSubmit,
        getValues,
        reset: resetPasswordForm,
        formState: { errors: passwordErrors, isSubmitting: isPasswordSubmitting },
    } = useForm();

    useEffect(() => {
        getUserData()
            .then(setUserData)
            .catch((error) => setLoadError(error.message));
    }, []);

    const onEmailSubmit = async ({ email }) => {
        setEmailError('');
        setEmailMessage('');

        try {
            const result = await updateUser({ email });
            setEmailMessage(result.message);
        } catch (error) {
            setEmailError(error.message);
        }
    };

    const onPasswordSubmit = async ({ password, passwordRepeated }) => {
        setPasswordError('');
        setPasswordMessage('');

        try {
            const result = await updateUser({
                password,
                'password-repeated': passwordRepeated,
            });
            setPasswordMessage(result.message);
            resetPasswordForm();
        } catch (error) {
            setPasswordError(error.message);
        }
    };

    const onDeleteAccount = async () => {
        if (!window.confirm('Are you sure you want to permanently delete your account?')) {
            return;
        }

        setDeleteError('');
        setIsDeleting(true);

        try {
            await deleteUser();
            removeStoredToken();
            navigate('/login', { replace: true });
        } catch (error) {
            setDeleteError(error.message);
            setIsDeleting(false);
        }
    };

    return (
        <main className="login-container">
            <section className="login-form account-form" aria-labelledby="account-heading">
                <h2 id="account-heading">My account</h2>
                {loadError ? (
                    <p className="error-message" role="alert">{loadError}</p>
                ) : !userData ? (
                    <p role="status">Loading account details...</p>
                ) : (
                    <>
                        <p>Current email: {userData.email}</p>
                        <form onSubmit={handleEmailSubmit(onEmailSubmit)}>
                            <h3>Change email</h3>
                            <div className="form-group">
                                <label htmlFor="new-email">New email</label>
                                <input
                                    type="email"
                                    id="new-email"
                                    autoComplete="email"
                                    {...registerEmail('email', {
                                        required: 'Email is required',
                                    })}
                                />
                                {emailErrors.email && (
                                    <span className="error">{emailErrors.email.message}</span>
                                )}
                            </div>
                            {emailMessage && <p role="status">{emailMessage}</p>}
                            {emailError && <p className="error-message" role="alert">{emailError}</p>}
                            <button type="submit" disabled={isEmailSubmitting}>
                                {isEmailSubmitting ? 'Updating...' : 'Update email'}
                            </button>
                        </form>

                        <form onSubmit={handlePasswordSubmit(onPasswordSubmit)}>
                            <h3>Change password</h3>
                            <div className="form-group">
                                <label htmlFor="new-password">New password</label>
                                <input
                                    type="password"
                                    id="new-password"
                                    autoComplete="new-password"
                                    {...registerPassword('password', {
                                        required: 'Password is required',
                                        minLength: { value: 4, message: 'Password must be at least 4 characters' },
                                        maxLength: { value: 10, message: 'Password must be no more than 10 characters' },
                                    })}
                                />
                                {passwordErrors.password && (
                                    <span className="error">{passwordErrors.password.message}</span>
                                )}
                            </div>
                            <div className="form-group">
                                <label htmlFor="repeat-password">Repeat new password</label>
                                <input
                                    type="password"
                                    id="repeat-password"
                                    autoComplete="new-password"
                                    {...registerPassword('passwordRepeated', {
                                        required: 'Please repeat your password',
                                        validate: (value) => value === getValues('password') || 'Passwords do not match',
                                    })}
                                />
                                {passwordErrors.passwordRepeated && (
                                    <span className="error">{passwordErrors.passwordRepeated.message}</span>
                                )}
                            </div>
                            {passwordMessage && <p role="status">{passwordMessage}</p>}
                            {passwordError && <p className="error-message" role="alert">{passwordError}</p>}
                            <button type="submit" disabled={isPasswordSubmitting}>
                                {isPasswordSubmitting ? 'Updating...' : 'Update password'}
                            </button>
                        </form>

                        <section className="account-danger-zone" aria-labelledby="delete-account-heading">
                            <h3 id="delete-account-heading">Delete account</h3>
                            <p>This permanently deletes your account.</p>
                            {deleteError && <p className="error-message" role="alert">{deleteError}</p>}
                            <button
                                type="button"
                                className="delete-account-button"
                                onClick={onDeleteAccount}
                                disabled={isDeleting}
                            >
                                {isDeleting ? 'Deleting...' : 'Delete account'}
                            </button>
                        </section>
                    </>
                )}
            </section>
        </main>
    );
}