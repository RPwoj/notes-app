import Login from './pages/Login.jsx'
import Register from './pages/Register.jsx'
import Notes from './pages/Notes.jsx'
import { useEffect, useState } from 'react'
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { isLoggedIn } from './api/user.js'

function AuthRoute({ children, requireLogin }) {
  const [loggedIn, setLoggedIn] = useState(null)
  const [error, setError] = useState(null)

  useEffect(() => {
    isLoggedIn().then(setLoggedIn).catch(setError)
  }, [])

  if (error) return <p role="alert">{error.message}</p>
  if (loggedIn === null) return null
  if (loggedIn !== requireLogin) {
    return <Navigate to={requireLogin ? '/login' : '/notes'} replace />
  }
  return children
}

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route
          path="/login"
          element={<AuthRoute requireLogin={false}><Login /></AuthRoute>}
        />
        <Route
          path="/notes"
          element={<AuthRoute requireLogin><Notes /></AuthRoute>}
        />
        <Route path="/register" element={<Register />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App
