function NoteItem({ note }) {
  return (
    <article className="notes-card">
      <div className="notes-card__body">
        <h2 className="notes-card__title">{note.name}</h2>
        <p className="notes-card__text">{note.content}</p>
      </div>
      <div className="notes-card__actions">
        <span className="notes-card__action notes-card__action--edit">edit</span>
        <span className="notes-card__action notes-card__action--delete">delete</span>
      </div>
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
