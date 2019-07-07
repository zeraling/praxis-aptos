
-- creacion de base de datos 
create database dataHotel;

-- seleccion de la base de datos
use dataHotel;

----------------------------
-- creacion de tablas
----------------------------

create table apartamentos(
	numero int not null,
	created_at datetime not null,
	primary key (numero)
);


create table habitantes_aptos(
	id int auto_increment not null,
	numero_apto int not null,
	id_persona int not null,
	created_at datetime not null,
	updated_at datetime not null,
	primary key (id)
);


create table personas(
	id int not null,
	nombre varchar(200) not null,
	apellido varchar(200) not null
	primary key (id)
);


create table visitas(
	id int auto_increment not null,
	id_persona_visita int not null,
	id_apto_visita int not null,
	id_persona_visitada int not null,
	vehiculo TINYINT(1) not null,
	created_at datetime not null,
	updated_at datetime not null,
	primary key (id)
);


alter table habitantes_aptos add constraint fk_habita_aptos
foreign key (numero_apto) references apartamentos (numero);

alter table habitantes_aptos add constraint fk_aptos
foreign key (id_persona) references personas (id);


alter table visitas add constraint fk_persona_visita
foreign key (id_persona_visita) references personas (id);

alter table visitas add constraint fk_visitas_aptos
foreign key (id_apto_visita) references apartamentos (numero);



insert into apartamentos(numero,created_at)values
(201,CURRENT_TIMESTAMP),
(202,CURRENT_TIMESTAMP),
(203,CURRENT_TIMESTAMP),
(301,CURRENT_TIMESTAMP),
(302,CURRENT_TIMESTAMP),
(303,CURRENT_TIMESTAMP),
(401,CURRENT_TIMESTAMP),
(402,CURRENT_TIMESTAMP),
(403,CURRENT_TIMESTAMP);










