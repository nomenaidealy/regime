# 📖 Explication du sujet — TP CodeIgniter 4

---

## 1. 🔐 Page de Login

Tu arrives sur `/login`, tu vois un formulaire avec **username et password déjà pré-remplis** (`admin` / `admin123`). Tu cliques "Connexion" → tu es redirigé vers la liste des étudiants.

---

## 2. 👨‍🎓 Liste des étudiants (`/etudiants`)

Tu vois tous les étudiants en base. Chaque étudiant est **cliquable**. Tu cliques sur un étudiant → tu vas sur sa page de notes.

---

## 3. 📊 Page de notes d'un étudiant (`/etudiants/3/notes`)

C'est la page **la plus importante** du sujet. Tu vois des **onglets** :

```
[ S3 ] [ S4 Dev ] [ S4 BDRés ] [ S4 Web ] [ L2 Dev ] [ L2 BDRés ] [ L2 Web ]
```

**Onglet S3** → affiche juste les notes du semestre 3 (commun à tous)

**Onglets S4** → affiche les notes du semestre 4 selon le parcours choisi :
- S4 Dev = matières du parcours Développement
- S4 BDRés = matières du parcours Bases de Données et Réseaux
- S4 Web = matières du parcours Web et Design

**Onglets L2** → affiche les notes des **2 semestres ensemble** (S3 + S4) et calcule une **moyenne générale**.
Ex : L2 Dev = S3 + S4 Dev + moyenne des deux.

---

## 4. 📝 Formulaire saisie note (`/notes/ajouter`)

Accessible depuis le menu. Tu choisis :
- l'étudiant
- le cours
- la note

Tu peux soumettre **plusieurs fois** la même matière pour le même étudiant (pas bloqué). C'est voulu, parce que la règle dit :

> **→ On prend toujours la note maximale**

Donc si tu saisis `8`, puis `14`, puis `11` pour la même matière → le système retient **14**.

---

## 5. 📐 Les 2 règles de gestion importantes

**Règle 1 — Note max par matière**
```
Même matière saisie plusieurs fois → on garde MAX(note)
```
Cela se fait en SQL avec `MAX(note) GROUP BY cours_id`

**Règle 2 — Matières optionnelles**

Certains cours sont dans un groupe optionnel (ex: `S4_DEV_OPT1` = choisir 1 parmi INF204 / INF205 / INF206).
Le système choisit **automatiquement celle qui a la meilleure note** parmi le groupe.
```
GROUP BY groupe_option → on garde celle avec MAX(note)
```

---

## 6. 🔢 Calcul de la moyenne L2

```
Moyenne S3  = somme des notes S3 retenues / nombre de matières S3
Moyenne S4  = somme des notes S4 retenues / nombre de matières S4
Moyenne L2  = (Moyenne S3 + Moyenne S4) / 2
```

---

## 🗺️ Résumé du parcours utilisateur

```
/login
  └─→ /etudiants                 (liste)
        └─→ /etudiants/5/notes   (notes de l'étudiant 5)
              ├── onglet S3
              ├── onglet S4 Dev / BDRés / Web
              └── onglet L2 Dev / BDRés / Web  ← avec moyenne

/notes/ajouter                   (saisie depuis le menu, n'importe quand)
```
