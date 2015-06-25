# Contributing

This document is the set of guidelines for contributing to Plow.
For instructions on how to develop new commands, see [CREATING_COMMANDS.md](CREATING_COMMANDS.md).

## Coding standards

Plow is developed following the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) community standards.
All code contributions should be formatted accordingly.

## Bug reports and feature requests

Both can be submitted on GitHub.
Please remember this is an open-source project, and nobody is obligated to address either.
Bugs with steps to reproduce (or, even better, a failing test case) will generally be prioritized over those without.
Feature requests with context and use cases will also tend to be considered with higher priority.

## Adding features

New features should be based off accepted feature request issues, and submitted via a Pull Request.
Please address only one feature per PR; we strongly believe in the idea of "one commit is one feature" on master (PRs will be landed as squash-commits).
Whenever possible, please include test cases to support the changes.

**Important!** Changes that cause a backwards compatibility break should indicate as such, since they will force a version change.

Note: this doesn't mean to consolidate the PR into a single commit - use as many as you need to work effectively.

## Adding tests

Thank you! Please submit test cases via Pull Requests.