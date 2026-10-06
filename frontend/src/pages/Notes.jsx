import { useState, useEffect } from 'react'
import NotesList from '../components/NotesList.jsx'
import { getAllUserNotes } from '../api/note.js'

export default function Notes() {
  const [notes, setNotes] = useState([]);

  useEffect(() => {
    getAllUserNotes()
      .then((notes) => {
        console.log('Fetched notes:', notes);
        setNotes(notes);
      })
      .catch((error) => {
        console.error('Error fetching notes:', error);
      });
  },[]);
  
  return (
    <main>
      <h1>Notes</h1>
      <NotesList notes={notes} />
    </main>
  )
}
