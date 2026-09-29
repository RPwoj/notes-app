import NotesList from '../components/NotesList.jsx'

const exampleNotes = [
  { id: 1, name: 'Plan the next feature', content: 'Write down the first implementation steps.' },
  { id: 2, name: 'Review the notes', content: 'Check the latest updates and open questions.' },
  { id: 3, name: 'Ship the change', content: 'Run the checks and prepare the release.' },
]

export default function Notes() {
  return (
    <main>
      <h1>Notes</h1>
      <NotesList notes={exampleNotes} />
    </main>
  )
}
