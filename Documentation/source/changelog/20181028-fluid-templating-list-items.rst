Feature "Added fluid partials for list items"
=============================================

When using a seperate partial for ListItem you can simply adjust for your custom page type:

Example ListItem.html::
-----------------------
.. code-block:: html
    :linenos:

    <html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
          data-namespace-typo3-fluid="true">

    <f:comment>
        Add opening for possible different partials based on Document types:
    </f:comment>

    <f:switch expression="{result.search_document_type}">
        <f:case value="your-document-type">{f:render(partial: 'Results/Item/YourDocumentType', arguments: {result: result})}</f:case>
        <f:case value="pages">{f:render(partial: 'Results/Item/Page', arguments: {result: result})}</f:case>
        <f:defaultCase>{f:render(partial: 'Results/Item/Unknown', arguments: {result: result})}</f:defaultCase>
    </f:switch>

    </html>
