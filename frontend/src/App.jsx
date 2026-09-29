import { useState } from 'react'
import heroImg from './assets/hero.png'
import reactLogo from './assets/react.svg'
import viteLogo from './assets/vite.svg'
import TaskList from './components/TaskList.jsx'
import { login } from './api/user.js'
import Login from './pages/Login.jsx'
import './App.css'
import { BrowserRouter, Routes, Route, Link } from 'react-router-dom'

const exampleTasks = [
  { id: 1, name: 'Plan the next feature', content: 'Write down the first implementation steps.' },
  { id: 2, name: 'Review the notes', content: 'Check the latest updates and open questions.' },
  { id: 3, name: 'Ship the change', content: 'Run the checks and prepare the release.' },
]

function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* <Route path="/" element={<Home />} /> */}
        <Route path="/login" element={<Login />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App
