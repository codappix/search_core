.. _development_dataprocessor:

Develop a new DataProcessor
===========================

Make sure you understood :ref:`concepts_dataprocessing`.

Each DataProcessor has to be a single class which implements
``Codappix\SearchCore\DataProcessing\ProcessorInterface``.

Make sure you support both, Frontend and Backend, as processors can be called during searching and
indexing. Therefore do not rely on e.g. ``TSFE``, make sure dependencies are met and your code will
work in both environments.

Dependency Injection is working for custom DataProcessors.
