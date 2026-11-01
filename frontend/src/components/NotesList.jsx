import NoteFormEdit from './NoteFormEdit.jsx'
import { useState } from 'react'

function NoteItem({ note }) {
  const [isEditing, setIsEditing] = useState(false);
  const [noteTitle, setNoteTitle] = useState(note.title)
  const [noteExcerpt, setNoteExcerpt] = useState(note.excerpt)

  return (
    <article className="notes-card">
      <div className="notes-card__body">
        <h2 className="notes-card__title">{noteTitle}</h2>
        <p className="notes-card__text">{noteExcerpt}</p>
      </div>
      <div className="notes-card__actions">
        <button onClick={
          () => setIsEditing(true)
        } className="notes-card__action notes-card__action--edit">edit</button>
        <span className="notes-card__action notes-card__action--delete">delete</span>
      </div>
      {isEditing && (
        <div className="edit-form-holder">
          <button className="notes-card__action notes-card__action--edit" onClick={() => setIsEditing(false)}>close</button >
          <NoteFormEdit id={note.id} editTitle={setNoteTitle} editExcerpt={setNoteExcerpt} />
        </div>
      )}
    </article>
  )
}

export default function NotesList({ notes }) {
  return (
    <section className="notes-list" aria-label="Your notes">
      {notes.length > 0 ? (
        notes.map((note) => <NoteItem key={note.id} note={note} />)
      ) : (
        <div className="notes-list__empty">
          <span className="notes-list__empty-mark" aria-hidden="true">✳</span>
          <h2>Nothing here yet</h2>
          <p>Your notes will show up here when you create them.</p>
        </div>
      )}
    </section>
  )
}
