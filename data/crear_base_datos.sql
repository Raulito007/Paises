
--Creamos la Base de Datos

CREATE DATABASE IF NOT EXISTS paises
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

--Seleccionamos la base de datos
USE paises;

--Creamos la tabla paises_del_mundo
CREATE TABLE paises_del_mundo (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre_del_pais VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    continente VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    capital VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    habitantes INT(11) DEFAULT NULL,
    superficie INT(11) DEFAULT NULL,
    pib_2024_usd BIGINT(20) DEFAULT NULL,
    emisiones_co2_2024_toneladas BIGINT(20) DEFAULT NULL,
    bandera VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    fecha_admision_onu DATE DEFAULT NULL,
    Wikipedia VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    Wiki_capital VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Importamos los datos de la base de datos
LOAD DATA INFILE '/ruta_al_archivo/paises.csv' --Aquí hay que poner en (ruta_al_archivo),
--la ruta de nuestro archivo paises.csv ejemplo: c/Users/MyUsuario/Documentos/paises.csv
INTO TABLE paises_del_mundo
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(id, nombre_del_pais, continente, capital, habitantes, superficie, pib_2024_usd, emisiones_co2_2024_toneladas, bandera, fecha_admision_onu, Wikipedia, Wiki_capital);

--Ahora nuestra base de datos tiene los campos y datos necesarios para trabajar