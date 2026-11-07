import NoteFormEdit from './NoteFormEdit.jsx'
import { useState } from 'react'
import { getNote, deleteNote } from '../api/note.js'

function NoteItemDetails({ noteDetails }) {
  return (
    <div className='note-details'>
      <div className="note-details__content">
        <p>{noteDetails.content}</p>
      </div>
      <div className="note-details__info">
        <p className="note-details__info--created">Created: {noteDetails?.created?.date?.split('.')[0]}</p>
        <p className="note-details__info--edited">Edited: {noteDetails?.edited?.date?.split('.')[0]}</p>
      </div>
    </div>
  )
}

function NoteItem({ note }) {
  const [isEditing, setIsEditing] = useState(false);
  const [noteTitle, setNoteTitle] = useState(note.title)
  const [noteExcerpt, setNoteExcerpt] = useState(note.excerpt)
  const [details, setDetails] = useState(null);
  const [detailsVisible, setDetailsVisible] = useState(false);
  const [detailsLoading, setDetailsLoading] = useState(false);
  const [detailsError, setDetailsError] = useState('');
  const [targetId, setTargetId] = useState(note.id);
  const [deleted, setDeleted] = useState(false);

  const showDetails = async () => {
    if (details) {
      setDetailsVisible(!detailsVisible);
      return;
    }

    setDetailsLoading(true);
    setDetailsError('');
    try {
      const noteDetails = await getNote(note.id);
      setDetails(noteDetails);
      setDetailsVisible(true);
    } catch (error) {
      console.error('Failed to load note details', error);
      setDetailsError('Unable to load note details. Please try again.');
    } finally {
      setDetailsLoading(false);
    }
  } 

  const deletePending = async () => {
    if (!confirm('Are You sure?')) {
      return;
    }
    deleteTargetNote(targetId);
  }
  
  const deleteTargetNote = async (targetId) => {
    deleteNote(targetId);
    setDeleted(true);
  }

  if (deleted) {
    return null;
  }

  return (
    <article
      note-id={note.id}
      className={`notes-card${details ? ' details-loaded' : ''}${detailsVisible ? ' details-visible' : ''}`}
    >
      <div className="notes-card__body">
        <h2 className="notes-card__title">{noteTitle}</h2>
        <p className="notes-card__text">{noteExcerpt}</p>
        {details && <NoteItemDetails noteDetails={details} />}
        <button
          className="btn btn--show-details"
          onClick={showDetails}
          disabled={detailsLoading}
          aria-expanded={detailsVisible}
        >
          {detailsLoading ? 'Loading...' : detailsVisible ? 'Show less' : detailsError ? 'Try again' : 'Show more'}
        </button>
        {detailsError && <p role="alert">{detailsError}</p>}
      </div>
      <div className="notes-card__actions">
        <button onClick={
          () => setIsEditing(true)
        } className="notes-card__action notes-card__action--edit">edit</button>
        <button className="notes-card__action notes-card__action--delete" onClick={deletePending}>delete</button>
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
