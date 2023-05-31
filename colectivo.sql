create database Colectivo;
use Colectivo;

-- Creación de la tabla logs
create table logs (
  id int auto_increment primary key,
  timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  message TEXT
);

-- Ejemplo de registro en la tabla de logs
insert into logs (message) values ('Creada la base de datos Colectivo.');

-- Ver todos los logs
select * from logs;

-- Creación de la tabla usuarios
create table usuarios (
  idUsuario int(10) auto_increment primary key,
  nombreUsuario varchar(20),
  tipoUsuario varchar(10),
  correoUsuario varchar(64) UNIQUE,
  contrasenaUsuario varchar(100)
);

-- Creación de la tabla rango_de_frecuencias
create table rango_de_frecuencias (
  idRango int(3) auto_increment primary key,
  rango_de_frecuencias varchar(100),
  idUsuario int(10),
  foreign key (idUsuario) references usuarios(idUsuario) ON DELETE CASCADE
);

-- Creación de la tabla logs_auditoria
create table logs_auditoria (
  idLogs_auditoria int(3) auto_increment primary key,
  fechaLogs_auditoria date,
  horaLogs_auditoria time,
  accionLogs_auditoria varchar(100),
  descripcionLogs_auditoria varchar(255),
  idUsuario int(10),
  nombreUsuario varchar(20),
  foreign key (idUsuario) references usuarios(idUsuario) ON DELETE CASCADE
);

-- Creación de la tabla calculos
create table calculos (
  idCalculos int(3) auto_increment primary key,
  idUsuario int(10),
  foreign key (idUsuario) references usuarios(idUsuario),
  idRango int(3),
  foreign key (idRango) references rango_de_frecuencias(idRango),
  fechaCalculos date,
  horaCalculos time,
  valorEntradaCalculos float(24),
  tipoCalculo varchar(64),
  resultadoCalculos double
);

-- Creación de la tabla registro
create table registro (
  idRegistro int(10) auto_increment primary key,
  ingresoRegistro time,
  salidaRegistro time,
  idUsuario int(10),
  foreign key (idUsuario) references usuarios(idUsuario)
);
-- Trigger creador de logs de tablas nuevas
DELIMITER //
CREATE PROCEDURE log_tablas_nuevas()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE tableName VARCHAR(255);
  DECLARE logMessage VARCHAR(255);
  DECLARE cur CURSOR FOR SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE';
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO tableName;
    IF done THEN
      LEAVE read_loop;
    END IF;
    SET logMessage = CONCAT('Se ha creado la tabla: ', tableName);
    -- Verificar si el mensaje ya existe en la tabla de logs
    IF NOT EXISTS (SELECT 1 FROM logs WHERE message = logMessage) THEN
      INSERT INTO logs (message) VALUES (logMessage);
    END IF;
  END LOOP;
  CLOSE cur;
END //
DELIMITER ;
SET GLOBAL event_scheduler = ON;
CREATE EVENT log_tablas_nuevas_event
  ON SCHEDULE EVERY 4 second
  DO CALL log_tablas_nuevas();

-- Ejemplo de registro en la tabla de logs_auditoria
INSERT INTO logs_auditoria (fechaLogs_auditoria, horaLogs_auditoria, accionLogs_auditoria, descripcionLogs_auditoria, idUsuario)
VALUES (CURDATE(), CURTIME(), 'Actualización de usuario test', 'Se ha actualizado el correo electrónico', 1);

-- Trigger creador de logs_de_auditoria: creacion de cuentas
DELIMITER //
create trigger log_cuenta_nueva after insert on usuarios
for each row
begin
  declare contLogs int;
  set contLogs = (select COUNT(*) from logs_auditoria where accionLogs_auditoria = 'Creación de usuario' and descripcionLogs_auditoria = CONCAT('Se ha creado el usuario: ', new.nombreUsuario));
  
  if contLogs = 0 then
    insert into logs_auditoria (fechaLogs_auditoria, horaLogs_auditoria, accionLogs_auditoria, descripcionLogs_auditoria, idUsuario, nombreUsuario)
values (CURDATE(), CURTIME(), 'Creación de usuario', CONCAT('Se ha creado el usuario: ', new.nombreUsuario), new.idUsuario, (SELECT nombreUsuario FROM usuarios WHERE idUsuario = new.idUsuario));
  end if;
end //
DELIMITER ;

-- Ejemplo de registro en la tabla de logs
insert into logs (message) values ('El usuario X ha creado un nuevo registro en la tabla calculos con el valor de entrada Y.');

-- Ver todos los logs de auditoria
select * from logs_auditoria;
-- Test Traer los ultimos 10 logs de auditoria
select * from logs_auditoria order by fechaLogs_auditoria desc, horaLogs_auditoria desc limit 10;
update logs_auditoria set nombreUsuario='webMaster' where idUsuario=1;
delete from logs_auditoria where idLogs_auditoria=2;
-- Eliminación de registros antiguos de la tabla de logs
delete from logs where timestamp < DATE_SUB(NOW(), interval 1 month);

-- Creación de la cuenta administradora
insert into usuarios (nombreUsuario, tipoUsuario, correoUsuario, contrasenaUsuario)
values ('webMaster', 'Admin', 'webMaster@admin.s5', '$2y$10$AhiJqY49sJLEythPbXkf5epjFHr3miLs3KWMGKOJWupxjOWsbSqaq');

-- Trabajar los usuarios
select * from usuarios;
update usuarios set idUsuario=1 where idUsuario=2;
delete from usuarios where idUsuario = 1;
select tipoUsuario, correoUsuario, contrasenaUsuario from usuarios where idUsuario = 4;
-- Eliminar la base de datos Colectivo
drop database Colectivo;
Adm1n15Tr4t0R
