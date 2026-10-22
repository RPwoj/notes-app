import { API_URL } from './config.js'

const TOKEN_KEY = 'auth_token'

export function getStoredToken() {
    return localStorage.getItem(TOKEN_KEY)
}

export async function isLoggedIn() {
    const token = getStoredToken()
    if (!token) return false

    const response = await fetch(`${API_URL}/check-login`, {
        headers: { Authorization: `Bearer ${token}` },
    })

    if (response.status === 401) {
        localStorage.removeItem(TOKEN_KEY)
        return false
    }
    if (!response.ok) {
        throw new Error(`Login check failed (HTTP ${response.status})`)
    }

    return true
}

export async function login(username, password) {
    const response = await fetch(`${API_URL}/login`, {
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

export async function register(username, password) {
    const response = await fetch(`${API_URL}/register`, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: username, password: password }),
    })

    const responseBody = await response.text()
    let data

    try {
        data = responseBody ? JSON.parse(responseBody) : {}
    } catch {
        throw new Error(`Registration failed (HTTP ${response.status})`)
    }

    if (!response.ok) {
        const errorMessage = Array.isArray(data.errors)
            ? data.errors.join(' ')
            : data.error || data.message || 'Registration failed'
        throw new Error(errorMessage)
    }

    return data
}