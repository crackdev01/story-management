// Vuex store
interface RootState {
    user: User | null;
    userLoaded: boolean;
}

// Http List Response from Backend
interface HttpResponseList<T> {
    data: T[];
    total: number;
}

// Http Response from Backend
interface HttpResponse<T> {
    data: T;
}

// Login Response
interface LoginData {
    user: User;
    token: string;
}

// Models
interface StoryUser {
    id: number;
    name: string;
}
interface Story {
    id: number;
    title: string;
    content: string;
    status: string;
    users: StoryUser[];
}

interface User {
    id: number;
    role: string;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}

// Forms
interface CreateForm {
    title: string;
    content: string;
}

interface EditForm {
    title: string;
    content: string;
    userIds: number[];
}

export {
    RootState,
    LoginData,
    HttpResponseList,
    HttpResponse,
    StoryUser,
    Story,
    User,
    CreateForm,
    EditForm,
}