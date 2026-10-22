import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { createNote } from '../api/note.js';

export default function NoteCreateForm(props) {
    const { register, handleSubmit, reset } = useForm();
    const [errorMessage, setErrorMessage] = useState('');

    const onSubmit = async (data) => {
        setErrorMessage('');
        try {
            const response = await createNote(data);
            if (response.id) {
                props.setAddedNote(response.id)
            }
            reset();
        } catch (error) {
            setErrorMessage(error.message);
        }
    };

    return (
        <div className="note-create-form">
            <form onSubmit={handleSubmit(onSubmit)}>
                <label htmlFor="title">Title</label>
                <input type="text" id="title" name="title" {...register('title', { required: 'Title is required' })} />
                <label htmlFor="content">Content</label>
                <textarea id="content" name="content" {...register('content', { required: 'Content is required' })}></textarea>
                <div className="error-message">
                    {errorMessage && <span>{errorMessage}</span>}
                </div>
                <button type="submit">Create Note</button>
            </form>
        </div>
    )
}