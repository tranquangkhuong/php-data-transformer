## Requirement

* PHP ext: mbstring

<br>

## Configuration key/value list

|Key name|Data type|Value|
|--------|--------|-----|
|`key`|string||
|`type`|string|`string` `date`|
|`required`|boolean|`true` `false`|
|`format`|string|date format. Eg: `d/m/Y` `Y/m/d` ...|
|`default`|string, number||
|`case`|string, array<[string]>||
|`group_value`|string|`merge` `split`|
|`group_value_position`|number||

<br>

`__[children]` contains multi config

<br>

## Example

```
[
  {
    "app1": {
      "key": "username",
      "type": "string",
      "case": "send"
    },
    "app2": {
      "key": "user_name",
      "type": "string",
      "case": [
        "send",
        "result"
      ]
    }
  },
  {
    "app1": {
      "key": "user_gender",
      "type": "string",
      "case": "send"
    },
    "app2": {
      "key": "user.gender",
      "type": "string",
      "case": [
        "send",
        "result"
      ]
    }
  },
  {
    "app1": {
      "key": "info",
      "type": "list",
      "case": "send"
    },
    "app2": {
      "key": "infomation",
      "type": "list",
      "case": [
        "send",
        "result"
      ]
    },
    "__[children]": [
      {
        "app1": {
          "key": "name",
          "type": "string",
          "case": "send"
        },
        "motcua": {
          "key": "fullname",
          "type": "string",
          "case": "send"
        }
      }
    ]
  }
]
```