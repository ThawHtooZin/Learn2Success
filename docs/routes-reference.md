# Routes Reference (Full Contract)

**Middleware key:** `G` = guest · `A` = auth · `S` = role:student · `T` = role:teacher · `M` = role:admin

| Status | Method | Path | Name | Middleware |
|--------|--------|------|------|------------|
| ✅ | GET | `/` | `home` | G |
| ✅ | GET | `/login` | `login` | G |
| ✅ | POST | `/login` | — | G |
| ✅ | POST | `/logout` | `logout` | A |
| ✅ | GET | `/student` | `student.dashboard` | A, S |
| ✅ | GET | `/student/weeks/{week}` | `student.weeks.show` | A, S |
| ✅ | GET | `/student/quizzes/{quiz}` | `student.quizzes.show` | A, S |
| ✅ | POST | `/student/quizzes/{quiz}/new-try` | `student.quizzes.new-try` | A, S |
| ✅ | GET | `/student/quizzes/{quiz}/play` | `student.quizzes.play` | A, S |
| ✅ | POST | `/student/submissions/{submission}/questions/{question}/audio` | `student.submissions.audio` | A, S |
| ✅ | POST | `/student/submissions/{submission}/questions/{question}/selection` | `student.submissions.selection` | A, S |
| ✅ | POST | `/student/submissions/{submission}/complete` | `student.submissions.complete` | A, S |
| ✅ | GET | `/teacher/submissions` | `teacher.submissions.index` | A, T |
| ✅ | GET | `/teacher/submissions/by-student` | `teacher.submissions.by-student` | A, T |
| ✅ | GET | `/teacher/submissions/{submission}` | `teacher.submissions.show` | A, T |
| ✅ | PUT | `/teacher/submissions/{submission}` | `teacher.submissions.update` | A, T |
| ✅ | GET | `/admin/users` | `admin.users.index` | A, M |
| ✅ | GET | `/admin/users/create` | `admin.users.create` | A, M |
| ✅ | POST | `/admin/users` | `admin.users.store` | A, M |
| ✅ | GET | `/admin/users/{user}/edit` | `admin.users.edit` | A, M |
| ✅ | PUT/PATCH | `/admin/users/{user}` | `admin.users.update` | A, M |
| ✅ | DELETE | `/admin/users/{user}` | `admin.users.destroy` | A, M |
| ✅ | GET | `/manage/quizzes` | `manage.quizzes.index` | A, M |
| ✅ | GET | `/manage/quizzes/create` | `manage.quizzes.create` | A, M |
| ✅ | POST | `/manage/quizzes` | `manage.quizzes.store` | A, M |
| ✅ | GET | `/manage/quizzes/{quiz}/edit` | `manage.quizzes.edit` | A, M |
| ✅ | PUT/PATCH | `/manage/quizzes/{quiz}` | `manage.quizzes.update` | A, M |
| ✅ | DELETE | `/manage/quizzes/{quiz}` | `manage.quizzes.destroy` | A, M |
| ✅ | GET | `/admin/weeks` | `admin.weeks.index` | A, M |
| ✅ | GET | `/admin/weeks/create` | `admin.weeks.create` | A, M |
| ✅ | POST | `/admin/weeks` | `admin.weeks.store` | A, M |
| ✅ | GET | `/admin/weeks/{week}/edit` | `admin.weeks.edit` | A, M |
| ✅ | PUT/PATCH | `/admin/weeks/{week}` | `admin.weeks.update` | A, M |
| ✅ | DELETE | `/admin/weeks/{week}` | `admin.weeks.destroy` | A, M |
| ✅ | POST | `/admin/weeks/{week}/quizzes` | `admin.weeks.quizzes.store` | A, M |
| ✅ | PUT | `/admin/weeks/{week}/quizzes/reorder` | `admin.weeks.quizzes.reorder` | A, M |
| ✅ | DELETE | `/admin/weeks/{week}/quizzes/{quiz}` | `admin.weeks.quizzes.destroy` | A, M |

✅ = implemented · ⬜ = pending
