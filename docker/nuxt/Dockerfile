FROM node:20-slim

ENV TZ Asia/Tokyo

WORKDIR /app

RUN apt-get update \
    && apt-get install -y \
    git \
    vim \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
