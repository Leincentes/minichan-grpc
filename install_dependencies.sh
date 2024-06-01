#!/bin/bash

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to install a package
install_package() {
    read -p "Do you want to install $1? (y/n) " answer
    if [ "$answer" != "${answer#[Yy]}" ]; then
        sudo apt-get install -y "$1"
    else
        echo "$1 installation skipped."
    fi
}

# Function to install a PHP extension using pecl
install_pecl_extension() {
    EXTENSION=$1
    if ! pecl list | grep -q "$EXTENSION"; then
        echo "$EXTENSION is not installed."
        read -p "Do you want to install $EXTENSION using pecl? (y/n) " answer
        if [ "$answer" != "${answer#[Yy]}" ]; then
            sudo pecl install "$EXTENSION"
            echo "extension=$EXTENSION.so" | sudo tee -a "$(php --ini | grep 'Loaded Configuration' | sed -e "s|.*:\s*||")"
        else
            echo "$EXTENSION installation skipped."
        fi
    else
        echo "$EXTENSION is already installed."
    fi
}

# Check PHP version
check_php_version() {
    REQUIRED_PHP_VERSION=$1
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2)

    if [[ -z "$PHP_VERSION" ]]; then
        echo "PHP is not installed."
        install_package php
    else
        if [[ "$(printf '%s\n' "$REQUIRED_PHP_VERSION" "$PHP_VERSION" | sort -V | head -n1)" != "$REQUIRED_PHP_VERSION" ]]; then
            echo "PHP version is sufficient (installed: $PHP_VERSION, required: $REQUIRED_PHP_VERSION)."
        else
            echo "PHP version $PHP_VERSION is less than required $REQUIRED_PHP_VERSION."
            install_package php
        fi
    fi
}

# Check if a package is installed and install if not
check_and_install() {
    if command_exists "$1"; then
        echo "$1 is already installed."
    else
        echo "$1 is not installed."
        install_package "$1"
    fi
}

# Check for PHP version 7.2 or higher (for version 4.8)
check_php_version 7.2

# Check for gcc version 4.8 or higher
REQUIRED_GCC_VERSION=4.8
GCC_VERSION=$(gcc --version | head -n 1 | awk '{print $3}')
if [[ -z "$GCC_VERSION" ]]; then
    echo "gcc is not installed."
    install_package gcc
else
    if [[ "$(printf '%s\n' "$REQUIRED_GCC_VERSION" "$GCC_VERSION" | sort -V | head -n1)" != "$REQUIRED_GCC_VERSION" ]]; then
        echo "gcc version is sufficient (installed: $GCC_VERSION, required: $REQUIRED_GCC_VERSION)."
    else
        echo "gcc version $GCC_VERSION is less than required $REQUIRED_GCC_VERSION."
        install_package gcc
    fi
fi

# Check for make
check_and_install make

# Check for autoconf
check_and_install autoconf

# Check for pecl
if command_exists pecl; then
    echo "pecl is already installed."
else
    echo "pecl is not installed."
    install_package php-pear
fi

# Install Swoole using pecl and modify php.ini
install_pecl_extension swoole

# Check for protoc
if command_exists protoc; then
    echo "protoc is already installed."
else
    echo "protoc is not installed."
    install_package protobuf-compiler
fi

# Check for inotify-tools
check_and_install inotify-tools

echo "All dependencies checked and necessary installations completed."
