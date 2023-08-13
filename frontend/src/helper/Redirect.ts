/**
 * Redirects to login page
 */
const redirectToLogin = () => {
    window.location.href = `${import.meta.env.VITE_APP_URL}/login`;
};

export default redirectToLogin;
