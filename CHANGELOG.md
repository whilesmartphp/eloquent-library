## [1.0.0] - 2026-07-12
- Polymorphic content library with a Collection -> Folder -> Asset hierarchy, scoped per owner via owner-access
- Open `kind` vocabulary on assets: text kinds use `body`, binary kinds carry a reference in `metadata`, no schema change to add a kind
- Presenter registry that turns an asset kind into listing and read shapes, with note, offering, profile, and default presenters
- Owner-scoped CRUD over HTTP for collections, folders, and assets
- Swappable models and configurable table names
