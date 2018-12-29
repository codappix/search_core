Feature "Added fluid partials for list items"
=============================================

When using a separate partial for ListItem you can simply adjust for your custom page type:

Example ListItem.html::
-----------------------
.. code-block:: html
    :linenos:

    <html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
          data-namespace-typo3-fluid="true">

    <f:comment>
        Add opening for possible different partials based on Document types:
    </f:comment>

    {f:render(partial: 'resultItem-{result.search_document_type}', arguments: {result: result)}

    <f:section name="resultItem-pages">
       // Render pages
    </f:section>

    <f:section name="resultItem-documentType">
       // Render custom "documentType"
    </f:section>

    </html>
