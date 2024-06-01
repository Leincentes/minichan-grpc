#!/bin/bash

# Check if the directory argument is provided
if [ $# -eq 0 ]; then
    echo "Usage: $0 <directory_to_watch>"
    exit 1
fi

# Directory to watch
WATCHED_DIR="$1"

# Command to start your server
START_SERVER_COMMAND="php minichan serve"

# Function to start the server
start_server() {
    echo "Starting server..."
    eval $START_SERVER_COMMAND &
    SERVER_PID=$!
}

# Function to restart the server
restart_server() {
    echo "Stopping server on port 9502..."
    kill $(lsof -t -i:9502)
    echo "Server stopped on port 9502."
    echo "Restarting server..."
    kill $SERVER_PID
    start_server
}

# Start the server initially
start_server

# Watch for file changes (including subdirectories)
inotifywait -r -m -e modify,create,delete,move "$WATCHED_DIR" |
while read path action file; do
    echo "Detected $action on $file. Restarting server..."
    restart_server
done
