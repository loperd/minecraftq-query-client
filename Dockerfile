FROM php:8.2-cli-alpine3.18

ARG USER=backend
ARG UID=1000
ARG GID=1000
ARG MODE=""

ENV \
    COMPOSER_ALLOW_SUPERUSER="1" \
    COMPOSER_HOME="/tmp/composer" \
    PROTOBUF_VERSION="3.20.1" \
    HOME='/home/backend'

# persistent / runtime deps
ENV PHPIZE_DEPS \
    build-base \
    autoconf \
    libc-dev \
    pcre-dev \
    pkgconf \
    cmake \
    file \
    re2c \
    g++ \
    gcc

# permanent deps
ENV PERMANENT_DEPS \
    linux-headers \
    oniguruma-dev \
    gettext-dev \
    libzip-dev \
    icu-dev \
    openssh \
    gmp-dev \
    libintl \
    bash \
    vim \
    git

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN set -xe \
    && apk add --no-cache ${PERMANENT_DEPS} \
    && apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} \
    # https://github.com/docker-library/php/issues/240
    && apk add --no-cache --repository https://dl-3.alpinelinux.org/alpine/edge/community gnu-libiconv \
    && docker-php-ext-configure mbstring --enable-mbstring \
    && docker-php-ext-configure bcmath --enable-bcmath \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure intl --enable-intl \
    # install PHP extensions (CFLAGS usage reason - https://bit.ly/3ALS5NU) \
    && CFLAGS="$CFLAGS -D_GNU_SOURCE" docker-php-ext-install -j$(nproc) \
        mbstring \
        sockets \
        gettext \
        bcmath \
        pcntl \
        intl \
    && apk del .build-deps \
    && rm -rf /app /home/user ${COMPOSER_HOME} /var/cache/apk/* \
    && mkdir --parents --mode=777 /src ${COMPOSER_HOME}/cache/repo ${COMPOSER_HOME}/cache/files \
    && ln -s /usr/bin/composer /usr/bin/c

RUN addgroup -g $GID $USER && adduser -D -h $HOME -s /bin/bash $USER -u $UID -G $USER && \
    apk add --no-cache sudo && \
    echo "backend ALL=(root) NOPASSWD:ALL" > /etc/sudoers.d/backend && \
    chmod 0440 /etc/sudoers.d/backend && \
    chown -R ${USER}:${USER} ${COMPOSER_HOME}

RUN ( \
        echo '#!/bin/bash' && \
        echo "" && \
        echo 'SLEEP_DELAY="${SLEEP_DELAY:-1}"' && \
        echo "" && \
        echo 'trap "echo SIGHUP" HUP' && \
        echo 'trap "echo Shutting down; exit" TERM' && \
        echo "" && \
        echo 'while :; do' && \
        echo '  sleep "$SLEEP_DELAY"' && \
        echo 'done' \
    ) > /keep-alive.sh && \
    ( \
        echo '#!/bin/bash' && \
        echo 'INPUT=("$@")' && \
        echo 'if [ "${#INPUT[@]}" -gt "1" ]; then' && \
        echo '  CMD=("$@")' && \
        echo 'else' && \
        echo '  CMD=($(echo "${INPUT[@]}" | tr '"' '"' " "))' && \
        echo 'fi' && \
        echo 'if [[ "${#CMD[@]}" != 0 && "${CMD[0]}" == "await" ]]; then' && \
        echo "  unset 'CMD[0]'" && \
        echo '  echo "[INFO] Await script will be runned"' && \
        echo '  [[ -n "$CMD" ]] && (CMD+=("&&"))' && \
        echo '  CMD+=(/keep-alive.sh)' && \
        echo 'else' && \
        echo '  echo "[INFO] Await script will not be runned"' && \
        echo 'fi' && \
        echo 'if [[ "${#CMD[@]}" != 0 ]]; then' && \
        echo '  echo "Executing command: ${CMD[@]}..."' && \
        echo '  EXEC_CMD="${CMD[@]}"' && \
        echo '  if [ $KEEP_ALIVE ]; then' && \
        echo '    EXEC_CMD="$EXEC_CMD && /keep-alive.sh"' && \
        echo '  fi' && \
        echo '  set -xe' && \
        echo '  exec /bin/bash -c "$EXEC_CMD"' && \
        echo 'else' && \
        echo '  echo "Command is empty, container shutting down..."' && \
        echo 'fi' \
    ) > /entrypoint.sh && \
    cat /keep-alive.sh /entrypoint.sh

RUN set -xe \
    && chmod +x /entrypoint.sh \
    && chmod +x /keep-alive.sh \
    && composer --version \
    && php -v \
    && php -m

WORKDIR /app

# DO NOT OVERRIDE ENTRYPOINT IF YOU CAN AVOID IT! @see <https://github.com/docker/docker.github.io/issues/6142>
ENTRYPOINT ["/entrypoint.sh"]
