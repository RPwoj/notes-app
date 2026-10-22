import { getStoredToken } from './user.js'
import { API_URL } from './config.js'

export async function getAllUserNotes() {
    const token = getStoredToken();

    const data = await fetch(`${API_URL}/note`, {
        method: 'GET',
        headers: {
            Authorization: `Bearer ${token}`
        },
    });

    const result = await data.json();

    return result;
}

export async function createNote(data) {
    const token = getStoredToken();
    console.log('test', data);

    const response = await fetch(`${API_URL}/note`, {
        method: 'POST',
        headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            title: data.title,
            content: data.content,
        }),
    });

    if (!response.ok) {
        throw new Error('Failed to create note');
    }

    const result = await response.json();
    return result;
}