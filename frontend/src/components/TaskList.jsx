
function ListItem({item}) {
    return (
        <div className="list-item">
            <div className="list-item__content">
                <p className="list-item__name">{item.name}</p>
                <p className="list-item__content">{item.content}</p>
            </div>
            <div className="list-item__actions">
                <span className="btn btn--edit"></span>
                <span className="btn btn--delete"></span>
            </div>
        </div>
    )
}

export default function List({ items }) {
  return (
    <div className="task-list">
        {items.map((item) => (
            <ListItem key={item.id} item={item} />
        ))}
    </div>
  )
}