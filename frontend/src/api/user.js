export async function login(username, password) {
    console.log('Logging in with username:', username, 'and password:', password);

    const response = await fetch('http://notes.local/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: username, password }),
    })

    const data = await response.json()

    if (!response.ok) {
        throw new Error(data.message || 'Login failed')
    }
    console.log('Login successful:', data);
    return data
}