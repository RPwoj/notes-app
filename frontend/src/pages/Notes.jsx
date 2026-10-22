import { useState, useEffect } from 'react'
import NotesList from '../components/NotesList.jsx'
import NoteCreateForm from '../components/NoteCreateForm.jsx'
import { getAllUserNotes } from '../api/note.js'

export default function Notes() {
  const [notes, setNotes] = useState([]);
  const [formVisible, setFormVisible] = useState(false);
  const [addedNote, setAddedNote] = useState(null);

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

  useEffect(() => {
    getAllUserNotes()
      .then((notes) => {
        console.log('Fetched notes:', notes);
        setNotes(notes);
      })
      .catch((error) => {
        console.error('Error fetching notes:', error);
      });
  },[addedNote]);
  
  
  const showForm = () => setFormVisible((visible) => !visible);

  return (
    <main className="notes-page">
      <header className="notes-page__header">
        <div>
          <p className="notes-page__eyebrow">Your space</p>
          <h1>Notes</h1>
          <p className="notes-page__description">A calm place for everything on your mind.</p>
        </div>
        <span
          className="notes-page__count"
          aria-label={`${notes.length} ${notes.length === 1 ? 'note' : 'notes'}`}
        >
          {notes.length} {notes.length === 1 ? 'note' : 'notes'}
        </span>
      </header>
      <button className="" onClick={showForm} aria-expanded={formVisible}>
        {formVisible ? 'Hide note form' : 'Add note'}
      </button>
      <div
        className={`form-container${formVisible ? ' form-container--open' : ''}`}
        aria-hidden={!formVisible}
      >
        <NoteCreateForm setAddedNote={setAddedNote} />
      </div>
      <NotesList notes={notes} />
    </main>
  )
}
