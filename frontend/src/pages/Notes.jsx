import { useEffect, useState } from 'react'
import NotesList from '../components/NotesList.jsx'
import { checkIfUserIsLoggedIn } from '../api/user.js'

const exampleNotes = [
  { id: 1, name: 'Plan the next feature', content: 'Write down the first implementation steps.' },
  { id: 2, name: 'Review the notes', content: 'Check the latest updates and open questions.' },
  { id: 3, name: 'Ship the change', content: 'Run the checks and prepare the release.' },
]

export default function Notes() {
  const [isCheckingLogin, setIsCheckingLogin] = useState(true)
  const [isLoggedIn, setIsLoggedIn] = useState(false)

  useEffect(() => {
    checkIfUserIsLoggedIn()
      .then((response) => {
        setIsLoggedIn(response.authenticated)
        setIsCheckingLogin(false)

        if (!response.authenticated) {
        //   window.location.href = '/login'
        console.log('User is not logged in, redirecting to login page')
        }
      })
      .catch(() => {
        console.error('Error checking login status')
        // window.location.href = '/login'
      })
  }, [])

  if (isCheckingLogin || !isLoggedIn) {
    return null
  }

  return (
    <main>
      <h1>Notes</h1>
      <NotesList notes={exampleNotes} />
    </main>
  )
}
