# Client Demo Video Guide — Learn2Success

**Purpose:** Screen recordings to walk the client through each role and major feature.  
**App:** Learn2Success (responsive web — demo student flow on mobile width when possible).

---

## How many videos?

| Plan | Count | Best for |
|------|-------|----------|
| **Recommended** | **10 videos** | One focused clip per role area; easy to send individually |
| **Short client deck** | **6 videos** | Combine sections marked *Can merge* below |
| **Optional bonus** | **+1 video** | Full end-to-end story (admin → student → teacher) |

**Recommended total: 10 videos** (~35–45 min combined, or ~3–5 min each).

---

## Before you record

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run dev
php artisan serve
```

| Account | Password | Use in demos |
|---------|----------|----------------|
| *(guest)* | — | Welcome + login |
| `student` | password | Week 1 only (locked weeks visible) |
| `student_allweeks` | password | All 4 weeks unlocked |
| `teacher` | password | Grading queue |
| `admin` | password | Users, quizzes, weeks |

**Recording tips**

- Use **phone-sized viewport** (375–430px) for student clips; **desktop** for teacher/admin.
- Allow mic permission for **Speak** demos (or mention “student grants mic in browser”).
- Speak slowly; pause 1–2 seconds after each click.
- Show flash success messages after saves.

---

## Video list (recommended 10)

### Video 1 — Platform intro & sign-in  
**Role:** Guest · **~3 min** · Route: `/` → `/login`

**Use case:** A new visitor discovers the product and signs in.

| Step | Show |
|------|------|
| 1 | Welcome page: logo, tagline, carousel (swipe/dots), Listen / Speak / Succeed cards |
| 2 | Responsive layout: optional quick resize (mobile vs desktop) |
| 3 | **Sign in to start** → login form |
| 4 | Log in as **student** → lands on week journey (proves role redirect) |
| 5 | Log out (student bottom nav or staff header) |

**Say to client:** “Public marketing home; each role gets a different home after login.”

---

### Video 2 — Student week journey & unlocks  
**Role:** Student · **~4 min** · Account: `student` then `student_allweeks`

**Use case:** Student sees their learning path; weeks unlock over time from registration date.

| Step | Show |
|------|------|
| 1 | Log in as **`student`**: `/student` — vertical week path |
| 2 | Week 1 **unlocked**; Weeks 2–4 **locked** (lock state / days until unlock if shown) |
| 3 | Tap Week 1 → week quiz path (`/student/weeks/{week}`) |
| 4 | Quiz steps: order, **Choose** / **Speak** tags, time limit on card |
| 5 | Log out → log in as **`student_allweeks`** |
| 6 | All 4 weeks unlocked — same journey, more nodes active |

**Say to client:** “Unlock schedule is based on when the student joined; admins configure weeks.”

---

### Video 3 — Student: multiple choice quiz (auto-grade)  
**Role:** Student · **~4 min** · Account: `student_allweeks`

**Use case:** Student completes an all–multiple-choice quiz and gets instant score.

| Step | Show |
|------|------|
| 1 | Week 1 → open **Quick Choose** or **Grammar Choose** |
| 2 | Quiz detail: title, marks, question count, attempt list (empty or past tries) |
| 3 | **Start** → play UI, progress, timer if quiz has time limit |
| 4 | Answer each **Choose** question (single select), Next |
| 5 | Complete submission |
| 6 | Back on detail: status **Graded**, score visible, no teacher needed |

**Say to client:** “Pure multiple-choice quizzes grade automatically on submit.”

---

### Video 4 — Student: speaking / recording quiz  
**Role:** Student · **~5 min** · Account: `student_allweeks`

**Use case:** Student hears the question, records spoken answer, submits for teacher grading.

| Step | Show |
|------|------|
| 1 | Week 1 → **Speak Up** (recording-only quiz) |
| 2 | Start play → **Speak** question |
| 3 | **Listen** (TTS) optional |
| 4 | **Record** → countdown → speak → stop → playback |
| 5 | Re-record once (optional) → continue through all recording questions |
| 6 | Complete → detail shows **Pending** (awaiting teacher grade) |

**Say to client:** “Speaking answers are stored as audio; teachers grade later.”

---

### Video 5 — Student: mixed quiz, resume & retakes  
**Role:** Student · **~4 min** · Account: `student_allweeks`

**Use case:** Mixed Choose + Speak quiz; student can resume and start new tries.

| Step | Show |
|------|------|
| 1 | Open **Choose & Speak** quiz |
| 2 | Start try → answer 1 MC question → **leave** (log out or go home) |
| 3 | Return → **Continue** in-progress attempt |
| 4 | Finish MC + recording → submit → Pending |
| 5 | After graded (or use prior graded try): **New try** / **Play again** |
| 6 | Mention: only one in-progress attempt at a time |

**Say to client:** “Students can practice multiple times; progress saves mid-quiz.”

*Can merge with Video 3 or 4 if you want fewer student clips.*

---

### Video 6 — Teacher: grading queue & filters  
**Role:** Teacher · **~3 min** · Account: `teacher`

**Use case:** Teacher finds work ready to grade and browses by student.

| Step | Show |
|------|------|
| 1 | Login → **Grade** (`/teacher/submissions`) |
| 2 | Filters: **Ready to grade**, In progress, Graded, All |
| 3 | Open a **pending** submission (from Video 4/5 if recorded live) |
| 4 | **Students** sidebar → by-student grouped attempts |
| 5 | Open same student’s attempt from grouped view |

**Say to client:** “Teachers only grade; they don’t edit quiz content.”

---

### Video 7 — Teacher: grade a submission (marks & validation)  
**Role:** Teacher · **~5 min** · Account: `teacher`

**Use case:** Teacher listens to recordings, assigns marks within rules, adds feedback.

| Step | Show |
|------|------|
| 1 | Open submission with **Speak** answers |
| 2 | **Quiz details (read-only)** panel: total marks, max per question, description, week, types |
| 3 | Play **audio** for recording question |
| 4 | For MC (if mixed): show student choice vs correct |
| 5 | Enter marks → show **running total** bar |
| 6 | Try mark **over per-question max** → Save disabled / error |
| 7 | Fix marks → **Save grading** → success, status Graded |
| 8 | Per-question feedback + overall teacher feedback |

**Say to client:** “System enforces caps so total never exceeds quiz maximum.”

*Can merge with Video 6.*

---

### Video 8 — Admin: user management  
**Role:** Admin · **~3 min** · Account: `admin`

**Use case:** Admin provisions student, teacher, and admin accounts.

| Step | Show |
|------|------|
| 1 | Login → sidebar **Users** |
| 2 | List: username, role |
| 3 | **Create user**: username, password, role (student) |
| 4 | **Edit user**: change role or password |
| 5 | Mention: cannot delete own admin account |

**Say to client:** “Admins control who can access the platform.”

---

### Video 9 — Admin: quiz authoring  
**Role:** Admin · **~5 min** · Account: `admin`

**Use case:** Admin builds a quiz with Choose and Speak questions.

| Step | Show |
|------|------|
| 1 | **Quizzes** → list (title, week column, question count) |
| 2 | **Create quiz**: title, description, total marks, **time limit (seconds)** |
| 3 | Add **Recording (Speak)** question |
| 4 | Add **Multiple choice (Choose)** question: choices, one correct |
| 5 | Add/remove questions; save |
| 6 | **Edit** quiz — note: week assignment is **not** here (only time limit) |
| 7 | Optional: deactivate quiz (`is_active`) |

**Say to client:** “Only two question types: Choose and Speak.”

---

### Video 10 — Admin: week program setup  
**Role:** Admin · **~5 min** · Account: `admin`

**Use case:** Admin structures the student journey: weeks, unlock days, quiz order.

| Step | Show |
|------|------|
| 1 | **Weeks** → list (week #, unlock days, quiz count) |
| 2 | **Create week** → redirects to edit |
| 3 | Week fields: title, week number, unlock after days, sort order, active |
| 4 | **Add quiz to week** from dropdown |
| 5 | **Drag** to reorder (desktop) or ↑↓ (mobile) |
| 6 | **Remove** quiz from week (quiz still exists under Quizzes) |
| 7 | Log in as `student_allweeks` → show updated path on journey |

**Say to client:** “Weeks control what students see and when; quiz order is managed here.”

*Can merge Videos 9 + 10 into one “Admin content setup” (~8 min).*

---

### Bonus Video 11 — End-to-end story (optional)  
**Roles:** Admin → Student → Teacher · **~8–10 min**

**Use case:** Full lifecycle in one narrative.

1. Admin creates week + quiz + assigns to week  
2. Admin creates student user (or use fresh student)  
3. Student logs in, completes Speak quiz  
4. Teacher grades submission  
5. Student sees graded score on quiz detail  

---

## Short plan (6 videos)

| # | Combines | Topic |
|---|----------|--------|
| 1 | V1 | Welcome + login + role redirect |
| 2 | V2 + V3 | Student journey + MC auto-grade |
| 3 | V4 + V5 | Recording + mixed/resume/retakes |
| 4 | V6 + V7 | Teacher queue + grading |
| 5 | V8 | Admin users |
| 6 | V9 + V10 | Admin quizzes + weeks |

---

## Use-case matrix (what each role does)

| Use case | Guest | Student | Teacher | Admin |
|----------|-------|---------|---------|-------|
| View marketing / brand | ✅ | — | — | — |
| Sign in / out | ✅ | ✅ | ✅ | ✅ |
| Week journey & unlocks | — | ✅ | — | — |
| Take quiz (Choose / Speak) | — | ✅ | — | — |
| Auto-grade (all MC) | — | ✅ | — | — |
| Resume / retake attempts | — | ✅ | — | — |
| View own scores | — | ✅ | — | — |
| Grade submissions | — | — | ✅ | — |
| Mark validation & feedback | — | — | ✅ | — |
| Manage users | — | — | — | ✅ |
| Author quizzes | — | — | — | ✅ |
| Manage weeks & quiz order | — | — | — | ✅ |

---

## Suggested recording order

Record in **dependency order** so later videos reuse data:

1. **Admin** V8 → V9 → V10 (setup content & users)  
2. **Student** V2 → V3 → V4 → V5 (creates submissions)  
3. **Teacher** V6 → V7 (grade those submissions)  
4. **Guest** V1 (standalone)  
5. **Bonus** V11 last (optional full story)

---

## Checklist per video

- [ ] Correct test account logged in  
- [ ] No console errors / broken assets (`npm run dev` running)  
- [ ] Mic permission granted (student Speak demos)  
- [ ] Flash success message visible after saves  
- [ ] Title card or voiceover: role + feature name at start  
- [ ] End with 1-sentence “what this means for the client”

---

## Related docs

- [learn2earn-prd.md](learn2earn-prd.md) — full requirements  
- [implementation-status.md](implementation-status.md) — seed accounts & setup  
- [routes-reference.md](routes-reference.md) — all URLs  
- Flow docs under [flows/](flows/) — step-by-step QA for each epic
