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
  },

  getFavoritos(parametros: object) {
    return apiClient.get("/api/v1/tablero/indicadorFavorito", parametros);
  },

  getDatosIndicador(
    idIndicador: number,
    dimension: string,
    parametros: object
  ) {
    return apiClient.get(
      `/api/v1/tablero/datosIndicador/${idIndicador}/${dimension}`,
      { params: parametros }
    );
  },

  getClasificacionTecnica(idClasificacionUso: number) {
    return apiClient.get(
      `/api/v1/tablero/clasificacionTecnica?id=${idClasificacionUso}`
    );
  },

  getClasificacionUso() {
    return apiClient.get("/api/v1/tablero/clasificacionUso");
  },

  getIndicadoresClasificados(
    idClasificacionUso: number,
    idClasificacionTecnica: number
  ) {
    return apiClient.get(
      `/api/v1/tablero/listaIndicadores?tipo=clasificados&uso=${idClasificacionUso}&tecnica=${idClasificacionTecnica}`
    );
  },

  borrarSala(parametros: object) {
    return apiClient.post("/api/v1/tablero/borrarSala", parametros);
  },

  getIndicadoresBusqueda(patron: string) {
    return apiClient.get(
      "/api/v1/tablero/listaIndicadores?tipo=busqueda&busqueda=" + patron
    );
  },

  getIndicadoresNoClasificados() {
    return apiClient.get(
      "/api/v1/tablero/listaIndicadores?tipo=no_clasificados"
    );
  },

  guardarSala(datos: object) {
    return apiClient.post("/api/v1/tablero/guardarSala", datos);
  },

  guardarSalaAccion(idSala: number, datos: object) {
    return apiClient.post(`/api/v1/tablero/salaAccion/${idSala}`, datos);
  },

  guardarSalaCompartir(idSala: number, datos: object) {
    return apiClient.post(`/api/v1/tablero/comentarioSala/${idSala}`, datos);
  },

  getDatosCatalogo(dimension: string) {
    return apiClient.get(`/api/v1/tablero/datosCatalogo/${dimension}`);
  },

  getIndicadoresFavoritos() {
    return apiClient.get("/api/v1/tablero/listaIndicadores?tipo=favoritos");
  },

  getMapaDatos(nombreMapa: string) {
    return apiClient.get("/js/Mapas/" + nombreMapa);
  },

  getDatosCompletosIndicador(idIndicador: number, parte: number) {
    return apiClient.get(`/rest-service/data/${idIndicador}?parte=${parte}`);
  }
};
