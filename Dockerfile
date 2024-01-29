FROM ubuntu:latest

LABEL maintainer="Melvstein"

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y php

EXPOSE 8000

CMD ["php", "artisan", "serve"]