#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )"
VERSION=$(cat $SCRIPT_DIR/VERSION)

docker buildx create --use --name calendar_builder

docker buildx build \
    -t reg.hcpss.org/calendar/web:${VERSION} \
    -t reg.hcpss.org/calendar/web:latest \
    --platform=linux/arm64,linux/amd64 \
    -f docker/web/Dockerfile \
    --push .

docker buildx rm calendar_builder
