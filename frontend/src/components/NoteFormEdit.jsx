import { getNote, updateNote } from "../api/note.js";
import { useState, useEffect } from 'react';
import { useForm } from 'react-hook-form';

export default function NoteFormEdit(props) {
    
    const { register, handleSubmit, reset } = useForm();
    const [PreviousTitle, setPreviousTItle] = useState(false);
    const [PreviousContent, setPreviousContent] = useState(false);
    const [currentTitle, setCurrentTitle] = useState(false);
    const [currentContent, setCurrentContent] = useState(false);
    const [noteId, setNoteId] = useState(false);
    
    useEffect(() => {
        getNote(props.id)
        .then((note) => {
            setNoteId(props.id);
            setCurrentTitle(note.title);
            setPreviousTItle(note.title);
            setCurrentContent(note.content);
            setPreviousContent(note.content);
            reset({ noteId: props.id, title: note.title, content: note.content });
        }
        )
        .catch((error) => {
            console.log(error)
        })
    },[]);

    const update = async () => {
        if (currentTitle === PreviousTitle && currentContent === PreviousContent) {
            console.log('nothing changed');
            return
        }

        const data = await updateNote(noteId, currentTitle, currentContent);
        props.editTitle(currentTitle);
        props.editExcerpt(data.note.excerpt);
    }

    return (
        <div className="note-form note-form--edit">
            <form onSubmit={handleSubmit(update)}>
                <input type="hidden" {...register('noteId')} value={noteId} />
                <label htmlFor="title">Title</label>
                <input name="title" type="text" {...register('title')} value={currentTitle} onChange={e => setCurrentTitle(e.target.value)} />
                <label htmlFor="content">Content</label>
                <textarea name="content" {...register('content')} value={currentContent} onChange={e => setCurrentContent(e.target.value)} />
                <button type="submit">Save changes</button>
                <button>abort</button>
            </form>
        </div>
    )
}