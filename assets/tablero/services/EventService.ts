import axios from "axios";

const apiClient = axios.create({
  baseURL: "",
  withCredentials: false,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json"
  }
});

export default {
  getSalas(parametros: object) {
    return apiClient.get("/api/v1/tablero/listaSalas", { params: parametros });
  },
  getSalaAcciones(idSala: string) {
    return apiClient.get("/api/v1/tablero/salaAccion/" + idSala);
  },
  getSalaComentarios(idSala: string) {
    return apiClient.get("/api/v1/tablero/comentarioSala/" + idSala);
  },
  getSalaUsuarios(idSala: string) {
    return apiClient.get("/api/v1/tablero/usuariosSala/" + idSala);
  }
};
