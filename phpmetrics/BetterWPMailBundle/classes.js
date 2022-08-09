var classes = [
    {
        "name": "Snicco\\Bundle\\BetterWPMail\\BetterWPMailBundle",
        "interface": false,
        "abstract": false,
        "final": true,
        "methods": [
            {
                "name": "shouldRun",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "configure",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "register",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "bootstrap",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "alias",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "bindMailer",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "bindTransport",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "bindViewEngineRenderer",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "bindMailEvents",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "copyConfiguration",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 10,
        "nbMethods": 10,
        "nbMethodsPrivate": 5,
        "nbMethodsPublic": 5,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "wmc": 20,
        "ccn": 11,
        "ccnMethodMax": 4,
        "externals": [
            "Snicco\\Component\\Kernel\\Bundle",
            "Snicco\\Component\\Kernel\\ValueObject\\Environment",
            "Snicco\\Component\\Kernel\\Configuration\\WritableConfig",
            "Snicco\\Component\\Kernel\\Kernel",
            "Snicco\\Component\\Kernel\\Kernel",
            "Snicco\\Component\\Kernel\\Kernel",
            "Snicco\\Component\\Kernel\\Kernel",
            "Snicco\\Component\\BetterWPMail\\ValueObject\\MailDefaults",
            "Snicco\\Component\\BetterWPMail\\Renderer\\FilesystemRenderer",
            "Snicco\\Component\\BetterWPMail\\Renderer\\AggregateRenderer",
            "Snicco\\Component\\BetterWPMail\\Mailer",
            "Snicco\\Component\\Kernel\\Kernel",
            "Snicco\\Component\\BetterWPMail\\Testing\\FakeTransport",
            "Snicco\\Component\\BetterWPMail\\Transport\\WPMailTransport",
            "Snicco\\Component\\Kernel\\Kernel",
            "LogicException",
            "Snicco\\Bundle\\BetterWPMail\\TemplateEngineMailRenderer",
            "Snicco\\Component\\Kernel\\Kernel",
            "Snicco\\Bundle\\BetterWPMail\\MailEventsUsingBetterWPHooks",
            "Snicco\\Component\\BetterWPMail\\Event\\MailEventsUsingWPHooks",
            "Snicco\\Component\\BetterWPMail\\Event\\NullEvents",
            "Snicco\\Component\\Kernel\\Kernel",
            "RuntimeException"
        ],
        "parents": [],
        "implements": [
            "Snicco\\Component\\Kernel\\Bundle"
        ],
        "lcom": 5,
        "length": 149,
        "vocabulary": 34,
        "volume": 758.03,
        "difficulty": 13.09,
        "effort": 9924.6,
        "level": 0.08,
        "bugs": 0.25,
        "time": 551,
        "intelligentContent": 57.9,
        "number_operators": 48,
        "number_operands": 101,
        "number_operators_unique": 7,
        "number_operands_unique": 27,
        "cloc": 7,
        "loc": 103,
        "lloc": 96,
        "mi": 54.76,
        "mIwoC": 35.12,
        "commentWeight": 19.65,
        "kanDefect": 0.64,
        "relativeStructuralComplexity": 400,
        "relativeDataComplexity": 0.67,
        "relativeSystemComplexity": 400.67,
        "totalStructuralComplexity": 4000,
        "totalDataComplexity": 6.67,
        "totalSystemComplexity": 4006.67,
        "package": "Snicco\\Bundle\\BetterWPMail\\",
        "pageRank": 0,
        "afferentCoupling": 0,
        "efferentCoupling": 16,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Snicco\\Bundle\\BetterWPMail\\TemplateEngineMailRenderer",
        "interface": false,
        "abstract": false,
        "final": true,
        "methods": [
            {
                "name": "__construct",
                "role": "setter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "render",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "supports",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getView",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 4,
        "nbMethods": 3,
        "nbMethodsPrivate": 1,
        "nbMethodsPublic": 2,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 1,
        "wmc": 6,
        "ccn": 4,
        "ccnMethodMax": 2,
        "externals": [
            "Snicco\\Component\\BetterWPMail\\Renderer\\MailRenderer",
            "Snicco\\Component\\Templating\\TemplateEngine",
            "Snicco\\Component\\BetterWPMail\\Exception\\CouldNotRenderMailContent",
            "Snicco\\Component\\Templating\\ValueObject\\View"
        ],
        "parents": [],
        "implements": [
            "Snicco\\Component\\BetterWPMail\\Renderer\\MailRenderer"
        ],
        "lcom": 1,
        "length": 44,
        "vocabulary": 12,
        "volume": 157.74,
        "difficulty": 8,
        "effort": 1261.91,
        "level": 0.13,
        "bugs": 0.05,
        "time": 70,
        "intelligentContent": 19.72,
        "number_operators": 12,
        "number_operands": 32,
        "number_operators_unique": 4,
        "number_operands_unique": 8,
        "cloc": 6,
        "loc": 43,
        "lloc": 37,
        "mi": 77.21,
        "mIwoC": 49.86,
        "commentWeight": 27.35,
        "kanDefect": 0.22,
        "relativeStructuralComplexity": 25,
        "relativeDataComplexity": 1.08,
        "relativeSystemComplexity": 26.08,
        "totalStructuralComplexity": 100,
        "totalDataComplexity": 4.33,
        "totalSystemComplexity": 104.33,
        "package": "Snicco\\Bundle\\BetterWPMail\\",
        "pageRank": 0,
        "afferentCoupling": 1,
        "efferentCoupling": 4,
        "instability": 0.8,
        "violations": {}
    },
    {
        "name": "Snicco\\Bundle\\BetterWPMail\\Option\\MailOption",
        "interface": false,
        "abstract": false,
        "final": true,
        "methods": [],
        "nbMethodsIncludingGettersSetters": 0,
        "nbMethods": 0,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 0,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "wmc": 0,
        "ccn": 1,
        "ccnMethodMax": 0,
        "externals": [],
        "parents": [],
        "implements": [],
        "lcom": 0,
        "length": 7,
        "vocabulary": 7,
        "volume": 19.65,
        "difficulty": 0,
        "effort": 0,
        "level": 2,
        "bugs": 0.01,
        "time": 0,
        "intelligentContent": 39.3,
        "number_operators": 0,
        "number_operands": 7,
        "number_operators_unique": 0,
        "number_operands_unique": 7,
        "cloc": 21,
        "loc": 32,
        "lloc": 11,
        "mi": 115.62,
        "mIwoC": 68.09,
        "commentWeight": 47.53,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 0,
        "relativeDataComplexity": 0,
        "relativeSystemComplexity": 0,
        "totalStructuralComplexity": 0,
        "totalDataComplexity": 0,
        "totalSystemComplexity": 0,
        "package": "Snicco\\Bundle\\BetterWPMail\\Option\\",
        "pageRank": 0,
        "afferentCoupling": 0,
        "efferentCoupling": 0,
        "instability": 0,
        "violations": {}
    },
    {
        "name": "Snicco\\Bundle\\BetterWPMail\\MailEventsUsingBetterWPHooks",
        "interface": false,
        "abstract": false,
        "final": true,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "fireSending",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "fireSent",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 3,
        "nbMethods": 3,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 3,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "wmc": 4,
        "ccn": 2,
        "ccnMethodMax": 2,
        "externals": [
            "Snicco\\Component\\BetterWPMail\\Event\\MailEvents",
            "Snicco\\Component\\EventDispatcher\\EventDispatcher",
            "Snicco\\Component\\BetterWPMail\\Event\\MailEventsUsingWPHooks",
            "Snicco\\Component\\BetterWPMail\\Event\\NullEvents",
            "Snicco\\Component\\BetterWPMail\\Event\\SendingEmail",
            "Snicco\\Component\\EventDispatcher\\GenericEvent",
            "Snicco\\Component\\BetterWPMail\\Event\\EmailWasSent"
        ],
        "parents": [],
        "implements": [
            "Snicco\\Component\\BetterWPMail\\Event\\MailEvents"
        ],
        "lcom": 1,
        "length": 19,
        "vocabulary": 6,
        "volume": 49.11,
        "difficulty": 1.7,
        "effort": 83.49,
        "level": 0.59,
        "bugs": 0.02,
        "time": 5,
        "intelligentContent": 28.89,
        "number_operators": 2,
        "number_operands": 17,
        "number_operators_unique": 1,
        "number_operands_unique": 5,
        "cloc": 0,
        "loc": 21,
        "lloc": 21,
        "mi": 59.05,
        "mIwoC": 59.05,
        "commentWeight": 0,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 9,
        "relativeDataComplexity": 0.33,
        "relativeSystemComplexity": 9.33,
        "totalStructuralComplexity": 27,
        "totalDataComplexity": 1,
        "totalSystemComplexity": 28,
        "package": "Snicco\\Bundle\\BetterWPMail\\",
        "pageRank": 0,
        "afferentCoupling": 1,
        "efferentCoupling": 7,
        "instability": 0.88,
        "violations": {}
    }
]