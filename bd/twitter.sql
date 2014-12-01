drop table if exists usuarios cascade;

create table usuarios (
    id      bigserial   constraint pk_usuarios primary key,
    nick    varchar(15) not null constraint uq_usuarios_nick unique,
    pass    char(32)    not null constraint ck_pass_valida
                        check (length(pass) = 32)
);

drop table if exists twitts cascade;

create table twitts (
    id          bigserial       constraint pk_twitts primary key,
    mensaje     varchar(140)    not null,
    fecha       timestamp       not null default current_timestamp,
    usuario_id  bigint          not null constraint fk_twitts_usuarios
                                references usuarios (id) on delete cascade
                                on update cascade
);

