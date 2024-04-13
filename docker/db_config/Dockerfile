FROM postgres:latest

ENV POSTGRES_DB comparison_db
ENV POSTGRES_USER postgres
ENV POSTGRES_PASSWORD postgres

COPY ./init.sql /docker-entrypoint-initdb.d/

EXPOSE 5432

COPY ./install-extensions.sql /docker-entrypoint-initdb.d
