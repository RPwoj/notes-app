function NoteItem({ note }) {
  return (
    <div className="list-item">
      <div className="list-item__content">
        <p className="list-item__name">{note.name}</p>
        <p className="list-item__content">{note.content}</p>
      </div>
      <div className="list-item__actions">
        <span className="btn btn--edit">edit</span>
        <span className="btn btn--delete">delete</span>
      </div>
    </div>
  )
}

export default function NotesList({ notes }) {
  return (
    <div className="notes-list">
      {notes.map((note) => (
        <NoteItem key={note.id} note={note} />
      ))}
    </div>
  )
}
