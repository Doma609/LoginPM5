# PMMP Login UI Plugin
======================

A simple and customizable login UI plugin for PocketMine-MP (PMMP) servers.

## Features
-----------

* Ui Register  And Login Form
* Easy to use and configure
* Login Reward


## Installation
------------

1. Download the plugin from the [Releases](https://github.com/Doma609/LoginPM5/releases) page.
2. Upload the plugin to your PMMP server's `plugins` directory.
3. Restart your server to load the plugin.

## Configuration
-------------

The plugin can be configured using the `config.yml` file located in the plugin's directory.

### Example Configuration
```yml
# Author: Doma
# GitHub: https://github.com/Doma0609

# Enable or disable login rewards (true: enabled, false: disabled)
# If true, when a player logs into the server, the console runs commands in the login-reward array
login-rewards: true

# If login-rewards is true, these commands run from the console when a player joins the server
# You can add more rewards, e.g., "50: 'msg {player} is this your fiftieth login into the server!'" to send a message to the player on their 50th login
login-reward:
  1: "give {player} diamond 1"
  20: "give {player} diamond 10"
  50: "give {player} dirt 64"

# Register Form Title
register_title: "§l§eRegister"

# Login Form Title
login_title: "§l§eLogin"

# Password mismatch error message in the register form
password_mismatch_kick_message: "Passwords do not match. Please try again."

# Error message displayed when an invalid password is entered during login
invalid_password_kick_message: "Password is incorrect.\n\n§cIf you forget your password, please contact the server staff."

# Registration success message
register_success_message: "You have successfully registered your account."

# Login success message
login_success_message: "Welcome back! You have successfully logged in."

# Reward message for login milestones
reward_message: "Congratulations! You have reached a login milestone of {milestone}. You received a reward."
```
