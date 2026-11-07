import Login from './pages/Login.jsx'
import Register from './pages/Register.jsx'
import Notes from './pages/Notes.jsx'
import { useEffect, useState } from 'react'
import { BrowserRouter, Routes, Route, Navigate, useNavigate } from 'react-router-dom'
import { isLoggedIn, removeStoredToken } from './api/user.js'

function AuthRoute({ children, requireLogin, allowLoggedIn = false }) {
  const [loggedIn, setLoggedIn] = useState(null)
  const [error, setError] = useState(null)

  useEffect(() => {
    isLoggedIn().then(setLoggedIn).catch(setError)
  }, [])

  if (error) return <p role="alert">{error.message}</p>
  if (loggedIn === null) return null
  if (loggedIn !== requireLogin && !(allowLoggedIn && loggedIn)) {
    return <Navigate to={requireLogin ? '/login' : '/notes'} replace />
  }
  return children
}

const logout = () => {
  removeStoredToken();
}

function Logout() {
  const navigate = useNavigate()

  useEffect(() => {
    logout()
    navigate('/login', { replace: true })
  }, [navigate])

  return null
}

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route
          path="/login"
          element={
            <AuthRoute
              requireLogin={false}
              allowLoggedIn={new URLSearchParams(window.location.search).get('confirmed') === '1'}
            >
              <Login />
            </AuthRoute>
          }
        />
        <Route
          path="/notes"
          element={<AuthRoute requireLogin><Notes /></AuthRoute>}
        />
        <Route path="/register" element={<Register />} />
        <Route path="/logout" element={<Logout />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App
