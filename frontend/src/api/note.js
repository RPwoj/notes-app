import { getStoredToken } from './user.js'

export async function getAllUserNotes() {
    const token = getStoredToken()

    const data = await fetch('http://notes.local/note', {
        method: 'GET',
        credentials: 'include',
        headers: {
            Authorization: `Bearer ${token}`
        },
    })

    const result = await data.json()

    return result
}