//import alasql from 'alasql';

export default function() {
  const normalizarDiacriticos = (value: string): string => {
    if (!value || value == undefined) return "";

    return value
      .toLowerCase()
      .normalize("NFD")
      .replace(/([aeio])\u0301|(u)[\u0301\u0308]/gi, "$1$2")
      .normalize();
  };

  return {
    normalizarDiacriticos
  };
}
