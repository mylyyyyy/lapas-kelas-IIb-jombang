---
name: Bug report
about: Create a report to help us improve
title: ''
labels: ''
assignees: ''

---

name: Laporan Bug 🐛
description: Laporkan error atau masalah pada sistem Lapas.
title: "[BUG] "
labels: ["bug", "high-priority"]
body:
  - type: markdown
    attributes:
      value: |
        Terima kasih sudah melaporkan masalah! Mohon isi detail di bawah ini.

  - type: input
    id: url
    attributes:
      label: URL / Halaman Error
      description: Di halaman mana error terjadi? (Contoh: /petugas/antrian)
      placeholder: http://...
    validations:
      required: true

  - type: textarea
    id: description
    attributes:
      label: Deskripsi Masalah
      description: Jelaskan apa yang terjadi dan apa yang seharusnya terjadi.
      placeholder: Saat saya klik tombol Simpan, layar menjadi putih dan loading tidak berhenti...
    validations:
      required: true

  - type: dropdown
    id: role
    attributes:
      label: Login Sebagai Siapa?
      options:
        - Admin Super
        - Petugas Kontrol (Control Room)
        - Petugas Loket (Front Office)
        - Pengunjung (Public)
    validations:
      required: true

  - type: dropdown
    id: browser
    attributes:
      label: Browser yang digunakan
      options:
        - Google Chrome
        - Microsoft Edge
        - Mozilla Firefox
        - Safari / Lainnya
