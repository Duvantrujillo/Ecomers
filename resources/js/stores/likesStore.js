export function registerLikesStore(Alpine) {
  Alpine.store('likes', {
    items: {},         // { "12": expiresAt, "33": expiresAt }
    ttlMinutes: 240,   // default

    init(ttlMinutes = 240) {
      this.ttlMinutes = ttlMinutes
      this.items = this._load()
      this._cleanupExpired()
    },

    _key() { return 'guest_likes_v1' },

    _load() {
      try {
        const raw = localStorage.getItem(this._key())
        if (!raw) return {}
        const data = JSON.parse(raw)
        return data && typeof data === 'object' ? data : {}
      } catch {
        return {}
      }
    },

    _save() {
      localStorage.setItem(this._key(), JSON.stringify(this.items))
    },

    _expiresAt() {
      return Date.now() + this.ttlMinutes * 60 * 1000
    },

    _cleanupExpired() {
      const now = Date.now()
      let changed = false

      for (const [id, exp] of Object.entries(this.items)) {
        if (!exp || now > exp) {
          delete this.items[id]
          changed = true
        }
      }

      if (changed) this._save()
    },

    isLiked(id) {
      this._cleanupExpired()
      return !!this.items[id]
    },

    toggle(id) {
      this._cleanupExpired()

      if (this.items[id]) {
        delete this.items[id]
        this._save()
        return false
      }

      this.items[id] = this._expiresAt()
      this._save()
      return true
    },

    get count() {
      this._cleanupExpired()
      return Object.keys(this.items).length
    }
  })
}