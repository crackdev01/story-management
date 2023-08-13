import axios from "axios";
import type { AxiosRequestConfig } from "axios";
import { API_TOKEN } from "../data/Constant";
import { getCookies } from "./Cookie";
import redirectToLogin from "./Redirect";


const httpRequest = axios.create({
    baseURL: import.meta.env.BACKEND_API_URL + "/api",
});

const tokenAssigner = (config: AxiosRequestConfig) => {
    const token = getCookies(API_TOKEN);

    if (token) {
        // eslint-disable-next-line no-param-reassign
        config.headers = {
            ...config.headers,
            Authorization: `Bearer ${token}`,
        };
    }

    return config;
};

httpRequest.interceptors.request.use(tokenAssigner);


httpRequest.interceptors.response.use(
    (response) => {        
        return response.data;
    },
    (error) => {       
        const message =
            error.response?.data?.message || error.message || error.toString();

        
        if (error.response.status === 401) {

            if (
                window.location.pathname !== "/login"
            ){
                
                redirectToLogin();
            }

            return Promise.reject(message);
        }

        return Promise.reject(message);
    }
);

export default httpRequest;
