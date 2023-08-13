# Kontinentalist Full-stack Engineer Test - Frontend

## Framework & Library

- Single Page App: [Vue 3](https://vuejs.org/) with TypeScript
- Router: [Vue Router](https://router.vuejs.org/)
- State Management: [Vuex](https://vuex.vuejs.org/)
- UI Library: [Vuetify](https://vuetifyjs.com/en/)
- Form Validation Library: [Vuelidate](https://vuelidate-next.netlify.app/)
- HTTP Client: [Axios](https://axios-http.com/docs/intro)

## Description
Simple Content Management System (CMS) for managing stories that consumes API from [Backend](https://github.com/alvintheodora/kontinentalist-story)
- Route:
    - `/login`: Login page
    - `/`: Dashboard page, listing stories available to logged in user.
    - `/story/:id`: Single Story page, showing story data and actions available to logged in user.
- [Navigation Guard](https://router.vuejs.org/guide/advanced/navigation-guards.html#Navigation-Guards) is applied to the routes where Unauthenticated user cannot navigate to Dashboard and Single Story page, and the Authenticated user will be redirected to Dashboard page if they try to access Login page.
- [Vuex](https://vuex.vuejs.org/) is applied for state management. In current case, mostly is used to store logged in user data across the pages.
- [Vuetify](https://vuetifyjs.com/en/) is applied for the web app to use the reusable UI Vue components.
- [Vuelidate](https://vuelidate-next.netlify.app/) is applied to enhance the handling of client-side form validation
- TypeScript is applied to improve type safety, tooling, and integration

## Requirement
* [Docker](https://docs.docker.com/install/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## Setup - Development

### **1. Clone the repository**

```bash
git clone https://github.com/alvintheodora/kontinentalist-story-vue.git
cd kontinentalist-story-vue
```

### **2. Scripts**

- `bash generate.sh` -> for first time installing and starting docker containers
- `bash start.sh` -> for starting docker containers on second time and so on 
- `bash stop.sh` -> for stopping running containers without removing them

Then web app will be available on [http://localhost:5173/](http://localhost:5173/)

### **3. Credentials**

- Admin Role
Email: admin@kontinentalist.com
Password: password
- User Role
Email: user@kontinentalist.com
Password: password
