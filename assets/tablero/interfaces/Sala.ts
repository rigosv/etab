import { Indicador } from "./Indicador";
export interface Sala {
  id: string;
  nombre: string;
  indicadores: Indicador[];
}