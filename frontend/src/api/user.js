const TOKEN_KEY = 'auth_token'

export function getStoredToken() {
    return localStorage.getItem(TOKEN_KEY)
}

export async function login(username, password) {
    console.log('Logging in with username:', username, 'and password:', password)

    const response = await fetch('http://notes.local/login', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: username, password }),
    })

    const data = await response.json()

    if (!response.ok) {
        throw new Error(data.message || 'Login failed')
    }

    if (data.token) {
        localStorage.setItem(TOKEN_KEY, data.token)
    }

    return data
}