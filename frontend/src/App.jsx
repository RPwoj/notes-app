import Login from './pages/Login.jsx'
import Register from './pages/Register.jsx'
import Notes from './pages/Notes.jsx'
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { getStoredToken } from './api/user.js'

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route
          path="/login"
          element={getStoredToken() ? <Navigate to="/notes" replace /> : <Login />}
        />
        <Route
          path="/notes"
          element={getStoredToken() ? <Notes /> : <Navigate to="/login" replace />}
        />
        <Route path="/register" element={<Register />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App
