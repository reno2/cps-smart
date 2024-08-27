#!/bin/bash

mkdir -p ~/.ssh
echo "$SSH_PRIVATE_KEY" > ~/.ssh/id_rsa
chmod 600 ~/.ssh/id_rsa
ssh-keyscan -H $REMOTE_HOST >> ~/.ssh/known_hosts

scp -r * $REMOTE_USER@$REMOTE_HOST:$WORKDIR

ssh $REMOTE_USER@$REMOTE_HOST << 'EOF'
  cd $WORKDIR
  docker-compose down
  docker-compose up -d
EOF
